/**
 * PURRSE Offline-First Architecture & IndexedDB Sync Engine
 * "Your cute little money companion."
 */
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('🐾 PURRSE ServiceWorker registered:', reg.scope))
                .catch(err => console.error('ServiceWorker registration error:', err));
        });
    }

    const DB_NAME = 'purrse-offline-db';
    const DB_VERSION = 2;
    let db;

    // Initialize IndexedDB with object stores
    function openDB() {
        return new Promise((resolve, reject) => {
            if (db) return resolve(db);
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = (e) => {
                const database = e.target.result;
                const stores = [
                    { name: 'profile', key: 'id' },
                    { name: 'budgets', key: 'uuid' },
                    { name: 'categories', key: 'uuid' },
                    { name: 'transactions', key: 'uuid' },
                    { name: 'incomes', key: 'uuid' },
                    { name: 'savings_goals', key: 'uuid' },
                    { name: 'savings_contributions', key: 'uuid' },
                    { name: 'settings', key: 'id' },
                    { name: 'sync_queue', key: 'uuid' }
                ];

                stores.forEach(s => {
                    if (!database.objectStoreNames.contains(s.name)) {
                        database.createObjectStore(s.name, { keyPath: s.key });
                    }
                });
            };

            request.onsuccess = (e) => {
                db = e.target.result;
                resolve(db);
            };

            request.onerror = (e) => reject(e);
        });
    }

    // Generic IndexedDB Helper Functions
    async function putItem(storeName, item) {
        await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            const req = store.put(item);
            req.onsuccess = () => resolve(req.result);
            req.onerror = (e) => reject(e);
        });
    }

    async function getAllItems(storeName) {
        await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readonly');
            const store = tx.objectStore(storeName);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result || []);
            req.onerror = (e) => reject(e);
        });
    }

    async function removeItem(storeName, key) {
        await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            const req = store.delete(key);
            req.onsuccess = () => resolve();
            req.onerror = (e) => reject(e);
        });
    }

    // Sync Engine Core
    class PurrseSyncEngine {
        constructor() {
            this.isSyncing = false;
            this.retryTimer = null;
        }

        async bootstrapLocalCache() {
            if (!navigator.onLine) return;
            try {
                const response = await fetch('/api/v1/bootstrap', {
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    const res = await response.json();
                    if (res.data) {
                        const d = res.data;
                        if (d.profile) await putItem('profile', d.profile);
                        if (d.settings) await putItem('settings', d.settings);
                        if (d.budgets) for (let b of d.budgets) await putItem('budgets', b);
                        if (d.categories) for (let c of d.categories) await putItem('categories', c);
                        if (d.transactions) for (let t of d.transactions) await putItem('transactions', t);
                        if (d.incomes) for (let i of d.incomes) await putItem('incomes', i);
                        if (d.savings_goals) for (let sg of d.savings_goals) await putItem('savings_goals', sg);
                        console.log('🐾 PURRSE local IndexedDB bootstrapped successfully.');
                    }
                }
            } catch (err) {
                console.warn('Bootstrap skipped:', err);
            }
        }

        async saveOfflineTransaction(payload, operation = 'CREATE') {
            const uuid = payload.uuid || crypto.randomUUID();
            payload.uuid = uuid;

            // 1. Save directly into local working IndexedDB store
            await putItem('transactions', payload);

            // 2. Queue in sync_queue
            const queueItem = {
                id: uuid,
                uuid: uuid,
                entity: 'transaction',
                entity_type: 'transaction',
                operation: operation,
                action: operation.toLowerCase(),
                payload: payload,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                retry_count: 0,
                status: 'PENDING'
            };
            await putItem('sync_queue', queueItem);

            // 3. Immediately update UI & offline dashboard calculations
            await this.calculateOfflineMetrics();

            // 4. Toast notification
            this.showToast(navigator.onLine ? 'Saved! Syncing to cloud...' : 'Saved offline ✓', 'success');

            // 5. Trigger sync if online
            if (navigator.onLine) {
                this.processSyncQueue();
            }

            return uuid;
        }

        async processSyncQueue() {
            if (this.isSyncing || !navigator.onLine) return;
            this.isSyncing = true;
            window.dispatchEvent(new CustomEvent('purrse:sync-start'));

            const queue = await getAllItems('sync_queue');
            const pendingItems = queue.filter(item => item.status === 'PENDING' || item.status === 'FAILED');

            if (pendingItems.length === 0) {
                this.isSyncing = false;
                window.dispatchEvent(new CustomEvent('purrse:sync-success'));
                return;
            }

            console.log(`🐾 Syncing ${pendingItems.length} queued offline records...`);

            // Mark items as SYNCING
            for (let item of pendingItems) {
                item.status = 'SYNCING';
                await putItem('sync_queue', item);
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch('/api/v1/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({ items: pendingItems })
                });

                if (response.ok) {
                    const res = await response.json();
                    for (let item of pendingItems) {
                        item.status = 'SYNCED';
                        await putItem('sync_queue', item);
                        await removeItem('sync_queue', item.uuid);
                    }
                    console.log('🐾 Sync complete! Items flushed from sync queue.');
                    window.dispatchEvent(new CustomEvent('purrse:sync-success'));
                    this.showToast('All changes synced ✓', 'success');
                } else {
                    throw new Error(`Server returned ${response.status}`);
                }
            } catch (err) {
                console.warn('Sync failed, queuing for retry with backoff:', err);
                for (let item of pendingItems) {
                    item.status = 'FAILED';
                    item.retry_count = (item.retry_count || 0) + 1;
                    await putItem('sync_queue', item);
                }
                window.dispatchEvent(new CustomEvent('purrse:sync-failed'));
                this.showToast("Some changes could not be synced. We'll retry.", 'warning');
                this.scheduleExponentialBackoff(pendingItems[0]?.retry_count || 1);
            } finally {
                this.isSyncing = false;
            }
        }

        scheduleExponentialBackoff(retryCount) {
            if (retryCount > 5) return;
            const delay = Math.min(Math.pow(2, retryCount) * 1000, 30000);
            console.log(`🐾 Scheduling sync retry in ${delay}ms...`);
            clearTimeout(this.retryTimer);
            this.retryTimer = setTimeout(() => {
                if (navigator.onLine) {
                    this.processSyncQueue();
                }
            }, delay);
        }

        async calculateOfflineMetrics() {
            const transactions = await getAllItems('transactions');
            const incomes = await getAllItems('incomes');
            const budgets = await getAllItems('budgets');

            let totalExpense = transactions.reduce((acc, t) => acc + (parseFloat(t.amount) || 0), 0);
            let totalIncome = incomes.reduce((acc, i) => acc + (parseFloat(i.amount) || 0), 0);
            let totalBudget = budgets.reduce((acc, b) => acc + (parseFloat(b.total_amount) || 0), 0);
            let remainingBudget = totalBudget - totalExpense;

            // Dispatch event for Alpine components / Dashboard updates
            window.dispatchEvent(new CustomEvent('purrse:offline-metrics-updated', {
                detail: {
                    totalExpense,
                    totalIncome,
                    totalBudget,
                    remainingBudget,
                    transactionsCount: transactions.length
                }
            }));
        }

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 right-4 z-50 px-4 py-2.5 rounded-2xl text-xs font-bold text-charcoal shadow-lg border transition-all duration-300 transform translate-y-2 ${
                type === 'success' ? 'bg-emerald-100 border-emerald-300' : 'bg-amber-100 border-amber-300'
            }`;
            toast.innerHTML = `🐾 ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }

    const engine = new PurrseSyncEngine();
    window.PurrseSyncEngine = engine;

    window.addEventListener('load', () => {
        engine.bootstrapLocalCache();
    });

    window.addEventListener('online', () => {
        engine.processSyncQueue();
    });
})();

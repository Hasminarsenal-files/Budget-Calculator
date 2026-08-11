<div x-data="networkStatusComponent()" x-init="init()" class="inline-flex items-center">
    <!-- Network Indicator Badge -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-xs border"
         :class="{
            'bg-emerald-50 text-emerald-800 border-emerald-200': status === 'online' || status === 'synced',
            'bg-amber-50 text-amber-900 border-amber-200': status === 'offline',
            'bg-sky-50 text-sky-900 border-sky-200': status === 'syncing'
         }">
        
        <!-- Status Icons -->
        <template x-if="status === 'online'">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Online</span>
            </span>
        </template>

        <template x-if="status === 'offline'">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full border-2 border-amber-500 bg-amber-200"></span>
                <span>Offline — saved on device</span>
            </span>
        </template>

        <template x-if="status === 'syncing'">
            <span class="flex items-center gap-1.5">
                <span class="animate-spin inline-block text-sky-600">↻</span>
                <span>Syncing...</span>
            </span>
        </template>

        <template x-if="status === 'synced'">
            <span class="flex items-center gap-1.5">
                <span class="text-emerald-600 font-extrabold">✓</span>
                <span>Synced</span>
            </span>
        </template>
    </div>
</div>

<script>
function networkStatusComponent() {
    return {
        status: navigator.onLine ? 'online' : 'offline',
        init() {
            window.addEventListener('online', () => {
                this.status = 'syncing';
                if (window.PurrseSyncEngine) {
                    window.PurrseSyncEngine.processSyncQueue().then(() => {
                        this.status = 'synced';
                        setTimeout(() => { this.status = 'online'; }, 3000);
                    }).catch(() => {
                        this.status = 'offline';
                    });
                } else {
                    setTimeout(() => { this.status = 'online'; }, 1500);
                }
            });

            window.addEventListener('offline', () => {
                this.status = 'offline';
            });

            window.addEventListener('purrse:sync-start', () => {
                this.status = 'syncing';
            });

            window.addEventListener('purrse:sync-success', () => {
                this.status = 'synced';
                setTimeout(() => { this.status = 'online'; }, 3000);
            });

            window.addEventListener('purrse:sync-failed', () => {
                this.status = 'offline';
            });
        }
    }
}
</script>

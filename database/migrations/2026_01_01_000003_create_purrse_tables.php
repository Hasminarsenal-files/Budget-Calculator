<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('icon')->default('tag');
            $table->string('color')->default('#FFDFD3');
            $table->enum('type', ['expense', 'income'])->default('expense');
            $table->timestamps();
        });

        // Budgets
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['monthly', 'weekly', 'daily', 'trip', 'event', 'project', 'custom'])->default('monthly');
            $table->decimal('total_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Budget Categories Allocation
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('budget_id')->constrained('budgets', 'uuid')->onDelete('cascade');
            $table->foreignUuid('category_id')->constrained('categories', 'uuid')->onDelete('cascade');
            $table->decimal('allocated_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        // Transactions (Expenses)
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('budget_id')->nullable()->constrained('budgets', 'uuid')->onDelete('set null');
            $table->foreignUuid('category_id')->constrained('categories', 'uuid')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->string('payment_method')->default('Cash');
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Incomes
        Schema::create('incomes', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('source');
            $table->date('date');
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Savings Goals
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('target_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->date('target_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->timestamps();
        });

        // Savings Contributions
        Schema::create('savings_contributions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('savings_goal_id')->constrained('savings_goals', 'uuid')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('contribution_date');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // Payment Methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('e-wallet');
            $table->string('icon')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Sync Queue for Offline PWA Sync
        Schema::create('sync_queues', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // create, update, delete
            $table->string('entity_type'); // transaction, income, budget, savings_goal
            $table->string('entity_id');
            $table->json('payload');
            $table->enum('status', ['pending', 'synced', 'failed'])->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        // User Settings
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('currency_symbol')->default('₱');
            $table->string('theme')->default('pastel');
            $table->boolean('notify_overbudget')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('sync_queues');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('savings_contributions');
        Schema::dropIfExists('savings_goals');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('budget_categories');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('categories');
    }
};

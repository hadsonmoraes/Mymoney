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
        Schema::create('recurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('category')->nullOnDelete();
            $table->string('name');
            $table->string('value');
            $table->enum('type', ['entrada', 'saida']);
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'yearly', 'custom']);
            $table->integer('interval')->default(1);
            $table->unsignedTinyInteger('anchor_day');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('max_occurrences')->nullable();
            $table->unsignedInteger('occurrences_count')->default(0);
            $table->date('next_run_date');
            $table->timestamp('last_generated_at')->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'canceled'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'next_run_date']);
        });

        Schema::table('contas', function (Blueprint $table) {
            $table->foreignId('recurrence_id')->nullable()->after('repeat')->constrained('recurrences')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->after('recurrence_id')->constrained('contas')->nullOnDelete();
            $table->string('repeat_group_id', 64)->nullable()->after('parent_id');
            $table->unsignedInteger('repeat_index')->nullable()->after('repeat_group_id');
            $table->unsignedInteger('repeat_total')->nullable()->after('repeat_index');

            $table->index(['user_id', 'repeat_group_id']);
            $table->index('recurrence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropForeign(['recurrence_id']);
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['user_id', 'repeat_group_id']);
            $table->dropIndex(['recurrence_id']);
            $table->dropColumn([
                'recurrence_id',
                'parent_id',
                'repeat_group_id',
                'repeat_index',
                'repeat_total',
            ]);
        });

        Schema::dropIfExists('recurrences');
    }
};

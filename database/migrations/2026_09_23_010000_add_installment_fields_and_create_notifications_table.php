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
        Schema::table('contas', function (Blueprint $table) {
            $table->string('installment_group_id', 64)->nullable()->after('repeat_total');
            $table->unsignedInteger('installment_number')->nullable()->after('installment_group_id');
            $table->unsignedInteger('installments_total')->nullable()->after('installment_number');

            $table->index(['user_id', 'installment_group_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('reminder_upcoming')->default(true)->after('darkmode');
            $table->unsignedTinyInteger('reminder_days_before')->default(3)->after('reminder_upcoming');
            $table->boolean('reminder_overdue')->default(true)->after('reminder_days_before');
        });

        Schema::create('internal_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conta_id')->nullable()->constrained('contas')->nullOnDelete();
            $table->enum('type', ['due_today', 'upcoming', 'overdue']);
            $table->string('title');
            $table->text('message');
            $table->date('reference_date');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->unique(['user_id', 'conta_id', 'type', 'reference_date'], 'user_conta_type_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_notifications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_upcoming',
                'reminder_days_before',
                'reminder_overdue',
            ]);
        });

        Schema::table('contas', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'installment_group_id']);
            $table->dropColumn([
                'installment_group_id',
                'installment_number',
                'installments_total',
            ]);
        });
    }
};

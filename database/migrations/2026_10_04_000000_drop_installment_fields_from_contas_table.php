<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove os campos do parcelamento estruturado da tabela contas.
     */
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'installment_group_id']);
            $table->dropColumn([
                'installment_group_id',
                'installment_number',
                'installments_total',
            ]);
        });
    }

    /**
     * Reverte a remoção recriando os campos do parcelamento estruturado.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->string('installment_group_id', 64)->nullable()->after('repeat_total');
            $table->unsignedInteger('installment_number')->nullable()->after('installment_group_id');
            $table->unsignedInteger('installments_total')->nullable()->after('installment_number');
            $table->index(['user_id', 'installment_group_id']);
        });
    }
};

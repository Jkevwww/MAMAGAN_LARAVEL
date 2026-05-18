<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('paymongo_checkout_id')->nullable()->after('reference_number')->index();
            $table->string('paymongo_payment_id')->nullable()->after('paymongo_checkout_id')->index();
            $table->text('checkout_url')->nullable()->after('paymongo_payment_id');
            $table->json('provider_payload')->nullable()->after('checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['paymongo_checkout_id']);
            $table->dropIndex(['paymongo_payment_id']);
            $table->dropColumn([
                'paymongo_checkout_id',
                'paymongo_payment_id',
                'checkout_url',
                'provider_payload',
            ]);
        });
    }
};

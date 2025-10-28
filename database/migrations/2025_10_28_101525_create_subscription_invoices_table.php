<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('subscription_package_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->integer('amount')->comment('in smallest currency unit (eg cents, paisa)');
            $table->string('currency',10)->nullable();
            $table->string('status')->nullable(); // paid, unpaid, failed
            $table->json('raw_payload')->nullable(); // raw event payload for debugging
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_invoices');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_subscription_id')->nullable();
            $table->dateTime('subscription_ends_at')->nullable();
            $table->integer('subscription_package_id')->nullable();
            $table->integer('customer_card_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_subscription_id');
            $table->dropColumn('subscription_ends_at');
            $table->dropColumn('subscription_package_id');
            $table->dropColumn('customer_card_id');
        });
    }
}

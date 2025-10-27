<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPackageFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_package_features', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_package_id')->nullable();
            $table->string('title')->nullable();
            $table->string('value')->nullable();
            $table->integer('sorting')->default(1);
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
        Schema::dropIfExists('subscription_package_features');
    }
}

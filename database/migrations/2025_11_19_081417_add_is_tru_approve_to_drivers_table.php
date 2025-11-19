<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTruApproveToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('is_tru_approve')->default(false);
            $table->boolean('is_tru_send')->default(false);
            $table->string('order_number')->nullable();
            $table->string('tru_system_order_id')->nullable();
            $table->longText('request_xml')->nullable();   // XML Sent
            $table->longText('response_xml')->nullable();  // XML Received
            $table->string('tru_status')->default('pending');  // pending, success, failed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('is_tru_approve');
            $table->dropColumn('is_tru_send');
            $table->dropColumn('order_number');
            $table->dropColumn('tru_system_order_id');
            $table->dropColumn('request_xml');
            $table->dropColumn('response_xml');
            $table->dropColumn('tru_status');
        });
    }
}


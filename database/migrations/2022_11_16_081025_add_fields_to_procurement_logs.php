<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProcurementLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurement_logs', function (Blueprint $table) {
               $table->string('procurement_status_id')->nullable();
               $table->string('invoice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('procurement_logs', function (Blueprint $table) {
               $table->dropColumn('procurement_status_id');
               $table->dropColumn('invoice');
        });
    }
}

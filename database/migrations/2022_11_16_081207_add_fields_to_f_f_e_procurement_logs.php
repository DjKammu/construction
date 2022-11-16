<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToFFEProcurementLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
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
        Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
                $table->dropColumn('procurement_status_id');
               $table->dropColumn('invoice');
        });
    }
}

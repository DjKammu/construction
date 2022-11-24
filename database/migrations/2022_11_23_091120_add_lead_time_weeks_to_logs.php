<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadTimeWeeksToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('procurement_logs', function (Blueprint $table) {
                $table->string('lead_time_weeks')->nullable();
        });
        Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
                $table->string('lead_time_weeks')->nullable();
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
               $table->dropColumn('lead_time_weeks');
            });
            Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
                   $table->dropColumn('lead_time_weeks');
            });
    }
}

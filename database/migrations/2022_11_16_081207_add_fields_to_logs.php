<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurement_logs', function (Blueprint $table) {
                $table->string('po_sent_file')->nullable();
        });
        Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
                $table->string('po_sent_file')->nullable();
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
               $table->dropColumn('po_sent_file');
        });
        Schema::table('f_f_e_procurement_logs', function (Blueprint $table) {
               $table->dropColumn('po_sent_file');
        });
    }
}

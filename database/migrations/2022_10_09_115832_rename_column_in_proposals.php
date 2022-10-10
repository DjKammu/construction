<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnInProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_f_e_proposals', function (Blueprint $table) {
             // $table->dropConstrainedForeignId('subcontractor_id');
             // $table->dropConstrainedForeignId('trade_id');
              $table->dropColumn('f_f_e_vendor_id');
              $table->dropColumn('f_f_e_trade_id');

             // $table->unsignedBigInteger('f_f_e_vendor_id');
             // $table->unsignedBigInteger('f_f_e_trade_id');
             // $table->foreign('f_f_e_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');
             // $table->foreign('f_f_e_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_f_e_proposals', function (Blueprint $table) {
            // $table->dropConstrainedForeignId('f_f_e_vendor_id');
            // $table->dropConstrainedForeignId('f_f_e_trade_id');
            // $table->unsignedBigInteger('subcontractor_id');
            // $table->foreign('subcontractor_id')->references('id')->on('subcontractors')
            // ->onDelete('cascade');
            // $table->unsignedBigInteger('trade_id');
            // $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
        });
    }
}

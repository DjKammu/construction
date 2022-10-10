<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInFFEProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_f_e_proposals', function (Blueprint $table) {
             $table->dropColumn('f_f_e_vendor_id');
             $table->dropColumn('f_f_e_trade_id');
        });

        Schema::table('f_f_e_proposals', function (Blueprint $table) {
             $table->unsignedBigInteger('f_f_e_vendor_id')->nullable();
             $table->foreign('f_f_e_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');
             $table->unsignedBigInteger('f_f_e_trade_id')->nullable();
             $table->foreign('f_f_e_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade');
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
             $table->dropConstrainedForeignId('f_f_e_vendor_id');
             $table->dropConstrainedForeignId('f_f_e_trade_id');
        });
    }
}

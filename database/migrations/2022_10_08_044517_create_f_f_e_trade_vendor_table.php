<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFFETradeVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_f_e_trade_f_f_e_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_f_e_trade_id');
            $table->foreign('f_f_e_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade');
            $table->unsignedBigInteger('f_f_e_vendor_id');
            $table->foreign('f_f_e_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');
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
        Schema::dropIfExists('f_f_e_trade_f_f_e_vendor');
    }
}

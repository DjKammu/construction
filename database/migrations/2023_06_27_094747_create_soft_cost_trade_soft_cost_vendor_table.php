<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftCostTradeSoftCostVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soft_cost_trade_soft_cost_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('soft_cost_trade_id');
            $table->foreign('soft_cost_trade_id')->references('id')->on('soft_cost_trades')->onDelete('cascade');
            $table->unsignedBigInteger('soft_cost_vendor_id');
            $table->foreign('soft_cost_vendor_id')->references('id')->on('soft_cost_vendors')->onDelete('cascade');
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
        Schema::dropIfExists('soft_cost_trade_soft_cost_vendor');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditChangeOrdersAmuntFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_orders', function (Blueprint $table) {
            $table->float('subcontractor_price',8,3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_orders', function (Blueprint $table) {
             $table->bigInteger('subcontractor_price')->change();
        });
    }
}

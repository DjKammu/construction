<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditProposalsAmuntFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposals', function (Blueprint $table) {
              $table->float('labour_cost',8,3)->change();
             $table->float('material',8,3)->change();
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
        Schema::table('proposals', function (Blueprint $table) {
             $table->bigInteger('labour_cost')->change();
             $table->bigInteger('material')->change();
             $table->bigInteger('subcontractor_price')->change();
        });
    }
}

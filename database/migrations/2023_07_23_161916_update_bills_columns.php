<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBillsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
             $table->float('total_subcontractor_payment',8,5)->change();
             $table->float('retainage_held',8,5)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::table('bills', function (Blueprint $table) {
             $table->float('total_subcontractor_payment',8,3)->change();
             $table->float('retainage_held',8,3)->change();
        });
    }
}

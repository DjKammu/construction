<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetainageFieldsToBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->float('total_subcontractor_payment',8,5)->nullable();
            $table->float('retainage_percentage',8,3)->nullable();
            $table->float('retainage_held',8,5)->nullable();
            $table->string('purchase_order')->nullable();
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
              $table->dropColumn('total_subcontractor_payment');
             $table->dropColumn('retainage_percentage');
             $table->dropColumn('retainage_held');
             $table->dropColumn('purchase_order');
        });
    }
}

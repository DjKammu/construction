<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBillsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           Schema::table('bills', function (Blueprint $table) {
                   $table->float('payment_amount',8,3)->change();
                  $table->float('total_amount',8,3)->change();
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
                    $table->float('payment_amount')->change();
                    $table->float('total_amount')->change();
                });
        }
}

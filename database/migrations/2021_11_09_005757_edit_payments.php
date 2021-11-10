<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
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
        Schema::table('payments', function (Blueprint $table) {
             $table->string('status')->nullable(false)->change();
             $table->bigInteger('payment_amount')->change();
             $table->bigInteger('total_amount')->change();
        });
    }
}

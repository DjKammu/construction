<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftFieldsToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
             $table->string('soft_cost_payment_id')->nullable(); 
             $table->string('soft_cost_bill_id')->nullable(); 
             $table->string('soft_cost_log_id')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('soft_cost_payment_id');
            $table->dropColumn('soft_cost_bill_id');
            $table->dropColumn('soft_cost_log_id');
        });
    }
}

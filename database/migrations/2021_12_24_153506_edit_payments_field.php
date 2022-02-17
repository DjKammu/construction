<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPaymentsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('proposal_id')->nullable()->change();
            $table->unsignedBigInteger('subcontractor_id')->nullable()->change();
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->unsignedBigInteger('trade_id')->nullable()->change();
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
             $table->unsignedBigInteger('proposal_id')->nullable(false)->change();
             $table->unsignedBigInteger('subcontractor_id')->nullable(false)->change();
             $table->unsignedBigInteger('project_id')->nullable(false)->change();
             $table->unsignedBigInteger('trade_id')->nullable(false)->change();
        });
    }
}

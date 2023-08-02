<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('soft_cost_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('soft_cost_proposal_id')->nullable()->change();
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->unsignedBigInteger('soft_cost_trade_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('soft_cost_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('soft_cost_proposal_id')->change();
            $table->unsignedBigInteger('project_id')->change();
            $table->unsignedBigInteger('soft_cost_trade_id')->change();
        });
    }
}

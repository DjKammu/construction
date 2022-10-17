<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeIdNullInFFEProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_f_e_proposals', function (Blueprint $table) {
            // $table->unsignedBigInteger('subcontractor_id')->change()->nullable(true);
            // $table->unsignedBigInteger('trade_id')->change()->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_f_e_proposals', function (Blueprint $table) {
           // $table->unsignedBigInteger('subcontractor_id')->change()->nullable(false);
           // $table->unsignedBigInteger('trade_id')->change()->nullable(false);
        });
    }
}

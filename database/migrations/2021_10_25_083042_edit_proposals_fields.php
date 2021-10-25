<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditProposalsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->bigInteger('labour_cost')->nullable()->default(0)->change();
            $table->bigInteger('material')->nullable()->default(0)->change();
            $table->bigInteger('subcontractor_price')->nullable()->default(0)->change();

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
            $table->bigInteger('labour_cost')->nullable(false)->default(null)->change();
            $table->bigInteger('material')->nullable(false)->default(null)->change();
            $table->bigInteger('subcontractor_price')->nullable(false)->default(null)->change();
        });
    }
}

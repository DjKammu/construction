<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('labour_cost');
            $table->bigInteger('material');
            $table->bigInteger('subcontractor_price');      
            $table->unsignedBigInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('trade_id');
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            // $table->foreign('trade_id')->references('trade_id')->on('subcontractor_trade')->onDelete('cascade');
            // $table->foreign('trade_id')->references('trade_id')->on('project_trade')->onDelete('cascade');
            $table->text('files')->nullable();
            $table->text('notes')->nullable(); 
            $table->tinyInteger('awarded')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposals');
    }
}

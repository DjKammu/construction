<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftCostProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soft_cost_proposals', function (Blueprint $table) {
            $table->id();

             $table->double('labour_cost')->nullable();
            $table->double('material')->nullable();
            $table->double('subcontractor_price')->nullable();      
           
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            $table->text('files')->nullable();
            $table->text('notes')->nullable(); 
            $table->tinyInteger('awarded')->default(0);
            $table->double('trade_budget')->nullable();
             $table->unsignedBigInteger('soft_cost_vendor_id')->nullable();
             $table->foreign('soft_cost_vendor_id')->references('id')->on('soft_cost_vendors')->onDelete('cascade');
             $table->unsignedBigInteger('soft_cost_trade_id')->nullable();
             $table->foreign('soft_cost_trade_id')->references('id')->on('soft_cost_trades')->onDelete('cascade');

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
        Schema::dropIfExists('soft_cost_proposals');
    }
}

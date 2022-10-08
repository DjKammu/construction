<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FFEChangeOrder;

class CreateFFEProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
     Schema::create('f_f_e_proposals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('labour_cost')->nullable();
            $table->bigInteger('material')->nullable();
            $table->bigInteger('subcontractor_price')->nullable();      
            $table->unsignedBigInteger('subcontractor_id');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('trade_id');
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            
            $table->text('files')->nullable();
            $table->text('notes')->nullable(); 
            $table->tinyInteger('awarded')->default(0);
            $table->string('trade_budget')->nullable();
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
        Schema::dropIfExists('f_f_e_proposals');
    }
}

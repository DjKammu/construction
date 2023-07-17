<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SoftCostChangeOrder;

class CreateSoftCostChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soft_cost_change_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(SoftCostChangeOrder::ADD);
            $table->unsignedBigInteger('subcontractor_price');     
            $table->text('notes')->nullable(); 
            $table->unsignedBigInteger('soft_cost_proposal_id');
            $table->foreign('soft_cost_proposal_id')->references('id')->on('soft_cost_proposals')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('soft_cost_change_orders');
    }
}

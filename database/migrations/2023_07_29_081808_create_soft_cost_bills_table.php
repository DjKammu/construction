<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftCostBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soft_cost_bills', function (Blueprint $table) {
            $table->id();

               $table->unsignedBigInteger('soft_cost_proposal_id')->nullable();
            $table->foreign('soft_cost_proposal_id')->references('id')->on('soft_cost_proposals')
                  ->onDelete('cascade');

           
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedBigInteger('soft_cost_trade_id')->nullable();
            $table->foreign('soft_cost_trade_id')->references('id')->on('soft_cost_trades')->onDelete('cascade'); 

            $table->unsignedBigInteger('soft_cost_vendor_id')->nullable();
            $table->foreign('soft_cost_vendor_id')->references('id')->on('soft_cost_vendors')->onDelete('cascade');

            $table->bigInteger('material_id')->nullable(); 

            $table->bigInteger('soft_cost_payment_id')->nullable(); 

            $table->double('payment_amount');
            $table->double('total_amount')->nullable();
            $table->string('invoice_number')->nullable(); 
            $table->string('purchase_order')->nullable(); 
            
            $table->date('date')->required();
            $table->text('notes')->nullable(); 
            $table->text('file')->nullable(); 
            $table->string('status')->nullable(); 
            $table->string('bill_status')->required()->default(\App\Models\FFEBill::UNPAID_BILL_STATUS); 
            $table->enum('non_contract',[0,1])->default(0);

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
        Schema::dropIfExists('soft_cost_bills');
    }
}

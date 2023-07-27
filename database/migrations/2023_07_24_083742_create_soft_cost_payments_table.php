<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftCostPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soft_cost_payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('soft_cost_proposal_id');
            $table->foreign('soft_cost_proposal_id')->references('id')->on('soft_cost_proposals')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedBigInteger('soft_cost_trade_id');
            $table->foreign('soft_cost_trade_id')->references('id')->on('soft_cost_trades')->onDelete('cascade'); 

            $table->unsignedBigInteger('soft_cost_vendor_id')->nullable();
            $table->foreign('soft_cost_vendor_id')->references('id')->on('soft_cost_vendors')->onDelete('cascade');

            $table->double('payment_amount');
            $table->double('total_amount');
            $table->string('invoice_number')->nullable();
            
            $table->date('date')->required();
            $table->text('notes')->nullable(); 
            $table->text('file')->nullable(); 
            $table->text('conditional_lien_release_file')->nullable(); 
            $table->text('unconditional_lien_release_file')->nullable(); 
            $table->enum('non_contract',[0,1])->default(0);
            $table->string('soft_cost_bill_id')->nullable();

            $table->string('status')->nullable(); 

            $table->double('total_subcontractor_payment')->nullable();
            $table->float('retainage_percentage')->nullable();
            $table->double('retainage_held')->nullable();
            $table->string('purchase_order')->nullable();

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
        Schema::dropIfExists('soft_cost_payments');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFFEBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('f_f_e_bills');
        Schema::create('f_f_e_bills', function (Blueprint $table) {
            $table->id();

             $table->unsignedBigInteger('ffe_proposal_id')->nullable();
            $table->foreign('ffe_proposal_id')->references('id')->on('f_f_e_proposals')
                  ->onDelete('cascade');

            // $table->unsignedBigInteger('ffe_subcontractor_id')->nullable();
            // $table->foreign('ffe_subcontractor_id')->references('id')->on('f_f_e_subcontractors')->onDelete('cascade');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('ffe_trade_id')->nullable();
            $table->foreign('ffe_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade'); 

            $table->unsignedBigInteger('ffe_vendor_id')->nullable();
            $table->foreign('ffe_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');
            $table->bigInteger('ffe_material_id')->nullable(); 

            $table->bigInteger('ffe_payment_id')->nullable(); 

            $table->double('payment_amount');
            $table->double('total_amount')->nullable();
            $table->string('invoice_number')->nullable(); 
            
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
        Schema::dropIfExists('f_f_e_bills');
    }
}

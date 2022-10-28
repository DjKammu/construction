<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Bill;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proposal_id')->nullable();
            $table->foreign('proposal_id')->references('id')->on('proposals')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('subcontractor_id')->nullable();
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('trade_id')->nullable();
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade'); 

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->bigInteger('material_id')->nullable(); 

            $table->bigInteger('payment_id')->nullable(); 

            $table->float('payment_amount',8,3);
            $table->float('total_amount',8,3)->nullable();
            $table->string('invoice_number')->nullable(); 
            
            $table->date('date')->required();
            $table->text('notes')->nullable(); 
            $table->text('file')->nullable(); 
            $table->string('status')->nullable(); 
            $table->string('bill_status')->required()->default(Bill::UNPAID_BILL_STATUS); 

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
        Schema::dropIfExists('bills');
    }
}

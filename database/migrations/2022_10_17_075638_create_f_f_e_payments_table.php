<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFFEPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_f_e_payments', function (Blueprint $table) {
            $table->id();

             $table->unsignedBigInteger('ffe_proposal_id')->nullable();
            $table->foreign('ffe_proposal_id')->references('id')->on('f_f_e_proposals')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedBigInteger('f_f_e_trade_id')->nullable();
            $table->foreign('f_f_e_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade'); 

            $table->unsignedBigInteger('f_f_e_vendor_id')->nullable();
            $table->foreign('f_f_e_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');

            $table->float('payment_amount');
            $table->float('total_amount');
            $table->string('invoice_number')->nullable();

            $table->date('date')->required();
            $table->text('notes')->nullable(); 
            $table->text('file')->nullable(); 
            $table->text('conditional_lien_release_file')->nullable(); 
            $table->text('unconditional_lien_release_file')->nullable(); 

            $table->string('status')->nullable(); 

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
        Schema::dropIfExists('f_f_e_payments');
    }
}

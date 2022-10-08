<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FFEChangeOrder;

class CreateFFEChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('f_f_e_change_orders');
        Schema::create('f_f_e_change_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(FFEChangeOrder::ADD);
            $table->unsignedBigInteger('subcontractor_price');     
            $table->text('notes')->nullable(); 
            $table->unsignedBigInteger('ffe_proposal_id');
            $table->foreign('ffe_proposal_id')->references('id')->on('f_f_e_proposals')
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
        Schema::dropIfExists('f_f_e_change_orders');
    }
}

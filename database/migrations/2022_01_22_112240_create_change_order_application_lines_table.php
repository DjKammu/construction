<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeOrderApplicationLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_order_application_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('change_order_application_id');
            $table->foreign('change_order_application_id','coa_id_foreign')->references('id')->on('change_order_applications')
            ->onDelete('cascade');

            $table->string('billed_to_date')->nullable();
            $table->string('stored_to_date')->nullable();
            $table->string('work_completed')->nullable();
            $table->string('materials_stored')->nullable();


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
        Schema::dropIfExists('change_order_application_lines');
    }
}

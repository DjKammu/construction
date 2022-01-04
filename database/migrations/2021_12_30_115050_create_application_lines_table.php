<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('application_id');
            $table->foreign('application_id')->references('id')->on('applications')
            ->onDelete('cascade');

            $table->unsignedBigInteger('project_line_id');
            $table->foreign('project_line_id')->references('id')->on('project_lines')
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
        Schema::dropIfExists('application_lines');
    }
}

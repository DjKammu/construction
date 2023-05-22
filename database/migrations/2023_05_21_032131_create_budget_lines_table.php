<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();

             $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->string('account_number')->nullable();
            $table->string('trade')->nullable();
            $table->string('price_sq_ft')->nullable();
            $table->string('budget')->nullable();

            
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
        Schema::dropIfExists('budget_lines');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRFISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_f_i_s', function (Blueprint $table) {
            $table->id();
            $table->string('number')->required();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('user_id')->nulled();
            $table->unsignedBigInteger('project_id');
            $table->date('date_sent')->nullable();
            $table->date('date_recieved')->nullable();
            $table->unsignedBigInteger('assign_to_id')->nullable();
            $table->string('subject')->nullable();
            $table->unsignedBigInteger('subcontractor_id')->nullable();
            $table->string('sent_file')->nullable();
            $table->string('recieved_file')->nullable();
            $table->unsignedBigInteger('ball_in_court_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('notes')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('r_f_i_s');
    }
}

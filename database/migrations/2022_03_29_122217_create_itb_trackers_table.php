<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItbTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itb_trackers', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedBigInteger('trade_id');
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');

            $table->unsignedBigInteger('subcontractors_id');
            $table->foreign('subcontractors_id')->references('id')->on('subcontractors')->onDelete('cascade');
                
            $table->tinyInteger('mail_sent')->default(0);
            $table->tinyInteger('bid_recieved')->default(0);
            $table->tinyInteger('contract_sign')->default(0);

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
        Schema::dropIfExists('itb_trackers');
    }
}

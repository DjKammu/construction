<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFFEITBTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_f_e_i_t_b_trackers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unsignedBigInteger('ffe_trade_id');
            $table->foreign('ffe_trade_id')->references('id')->on('f_f_e_trades')->onDelete('cascade');

            $table->unsignedBigInteger('ffe_vendor_id');
            $table->foreign('ffe_vendor_id')->references('id')->on('f_f_e_vendors')->onDelete('cascade');
                
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
        Schema::dropIfExists('f_f_e_i_t_b_trackers');
    }
}

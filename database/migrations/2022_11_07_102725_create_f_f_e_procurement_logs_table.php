<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFFEProcurementLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_f_e_procurement_logs', function (Blueprint $table) {
            $table->id();

            $table->date('date')->required();
            $table->string('item')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('ffe_vendor_id')->nullable();
            $table->unsignedBigInteger('ffe_subcontractor_id')->nullable();
            $table->date('po_sent')->nullable();
            $table->string('lead_time')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();

            $table->date('date_shipped')->nullable();
            $table->date('tentative_date_delivery')->nullable();
            $table->date('date_received')->nullable();

            $table->string('store_place')->nullable();

            $table->longText('received_shipment_attachment')->nullable();
            $table->string('notes')->nullable();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

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
        Schema::dropIfExists('f_f_e_procurement_logs');
    }
}

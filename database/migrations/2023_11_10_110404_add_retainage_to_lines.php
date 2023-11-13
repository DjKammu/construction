<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetainageToLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_lines', function (Blueprint $table) {
            $table->string('retainage')->nullable();
        });
        Schema::table('change_order_application_lines', function (Blueprint $table) {
          $table->unsignedBigInteger('retainage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_lines', function (Blueprint $table) {
            $table->dropColumn('retainage');
        }); 

        Schema::table('change_order_application_lines', function (Blueprint $table) {
            $table->dropColumn('retainage');
        });
    }
}

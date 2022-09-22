<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeinFieldsToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
               $table->text('conditional_lien_release_file')->nullable(); 
               $table->text('unconditional_lien_release_file')->nullable(); 
        });

        DB::table('document_types')->insert(
            array(
                'name' => 'Lien',
                'slug' =>  @\Str::slug('Lien'),
                'account_number' => 300
            )
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
             $table->dropColumn('conditional_lien_release_file');
             $table->dropColumn('unconditional_lien_release_file');
        });
    }
}

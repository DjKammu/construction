<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use  App\Models\DocumentType;

class InsertItemInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
             DB::table('document_types')->insert(
                array(
                    'name' => DocumentType::INVOICE,
                    'slug' => \Str::slug(DocumentType::INVOICE),
                    'account_number' => 200
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
        Schema::table('document_types', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
           $table->string('owner_name')->nullable();
           $table->string('owner_street')->nullable();
           $table->string('owner_city')->nullable();
           $table->string('owner_state')->nullable();
           $table->string('owner_zip')->nullable();

           $table->string('contract_name')->nullable();
           $table->string('contract_street')->nullable();
           $table->string('contract_city')->nullable();
           $table->string('contract_state')->nullable();
           $table->string('contract_zip')->nullable();

           $table->string('architect_name')->nullable();
           $table->string('architect_street')->nullable();
           $table->string('architect_city')->nullable();
           $table->string('architect_state')->nullable();
           $table->string('architect_zip')->nullable();

           $table->date('contract_date')->nullable();
           $table->string('project_date')->nullable();
           $table->float('retainage_percentage')->nullable();
           $table->float('original_amount')->nullable();
           $table->string('project_email')->nullable();

           $table->string('notary_name')->nullable();
           $table->string('notary_country')->nullable();
           $table->string('notary_state')->nullable();
           $table->date('commission_expire_date')->nullable();
           $table->string('status')->nullable(); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
             $table->dropColumn('owner_name');
             $table->dropColumn('owner_street');
             $table->dropColumn('owner_city');
             $table->dropColumn('owner_state');
             $table->dropColumn('owner_zip');
             $table->dropColumn('contract_name');
             $table->dropColumn('contract_street');
             $table->dropColumn('contract_city');
             $table->dropColumn('contract_state');
             $table->dropColumn('contract_zip');

             $table->dropColumn('architect_name');
             $table->dropColumn('architect_street');
             $table->dropColumn('architect_city');
             $table->dropColumn('architect_state');
             $table->dropColumn('architect_zip');
             
             $table->dropColumn('contract_date');
             $table->dropColumn('project_date');
             $table->dropColumn('retainage_percentage');
             $table->dropColumn('original_amount');
             $table->dropColumn('project_email');

             $table->dropColumn('notary_name');
             $table->dropColumn('notary_country');
             $table->dropColumn('notary_state');
             $table->dropColumn('commission_expire_date');
             $table->dropColumn('status');
        });
    }
}

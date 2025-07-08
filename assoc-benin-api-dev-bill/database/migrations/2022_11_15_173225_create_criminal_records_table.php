<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('criminal_records', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('casier')->nullable();
        //     $table->string('c_role_du_membre')->nullable();
        //     // $table->json('casier')->nullable();
        //     // $table->json('role_du_membre')->nullable();
        //     // $table->string('record_age')->nullable();
        //     $table->string('code_requete');
        //     $table->dateTime('created_at')->now();
        //     $table->dateTime('updated_at')->now();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('criminal_records');
    }
};

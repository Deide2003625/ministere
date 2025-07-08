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
        // Schema::create('residence_certificates', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('certificat')->nullable();
        //     $table->string('r_role_du_membre')->nullable();
        //     // $table->json('certificat')->nullable();
        //     // $table->json('role_du_membre')->nullable();
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
        // Schema::dropIfExists('residence_certificates');
    }
};

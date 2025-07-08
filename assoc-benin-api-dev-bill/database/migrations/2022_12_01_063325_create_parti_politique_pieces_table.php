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
        Schema::create('parti_politique_pieces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('acte_de_naissance')->nullable();
            $table->string('certificat_de_nationalite')->nullable();
            $table->string('code_requete');
            $table->dateTime('created_at')->now();
            $table->dateTime('updated_at')->now();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parti_politique_pieces');
    }
};

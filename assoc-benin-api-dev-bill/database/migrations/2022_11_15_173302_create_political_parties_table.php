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
        Schema::create('partis_politiques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('declaration')->nullable();
            $table->string('liste_de_presence')->nullable();
            $table->string('liste_de_createurs')->nullable();
            $table->string('projets_de_societe')->nullable();
            $table->string('fiche_de_description')->nullable();
            $table->string('logo_et_embleme')->nullable();
            $table->string('ideologie')->nullable();
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
        Schema::dropIfExists('partis_politiques');
    }
};

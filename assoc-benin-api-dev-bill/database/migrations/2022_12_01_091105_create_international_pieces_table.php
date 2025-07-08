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
        Schema::create('international_pieces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('duree_mandat')->nullable();
            $table->string('nom_du_responsable_etranger')->nullable();
            $table->string('contact_du_responsable_etranger')->nullable();
            $table->string('mandat')->nullable();
            $table->string('journal')->nullable();
            $table->string('recepisse_de_declaration')->nullable();
            $table->string('rapport_activites')->nullable();
            $table->string('adresse_benin')->nullable();
            $table->string('adresse_etranger')->nullable();
            $table->string('statuts_mere')->nullable();
            $table->string('reglement_interieur_mere')->nullable();
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
        Schema::dropIfExists('international_pieces');
    }
};

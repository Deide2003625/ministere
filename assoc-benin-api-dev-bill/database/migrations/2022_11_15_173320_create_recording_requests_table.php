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
        Schema::create('recording_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('statut_legal');
            $table->string('denomination');
            $table->string('acronyme');
            $table->string('reference_misp')->nullable();
            $table->string('reference_daic')->nullable();
            $table->dateTime('arrivee_misp')->nullable();
            $table->dateTime('arrivee_daic')->nullable();
            $table->date('date_ag')->nullable();
            $table->string('departement');
            $table->string('commune');
            $table->string('arrondissement');
            $table->string('quartier');
            $table->string('numero_lot');
            $table->string('immeuble');
            $table->string('boite_postale');
            $table->string('a_telephone');
            $table->string('a_email');
            $table->string('premier_responsable')->nullable();
            $table->string('email_premier_responsable')->nullable();
            // $table->string('membres_ag')->nullable();
            $table->string('objectifs');
            $table->string('observations')->default("pas encore")->nullable();
            $table->string('statut_requete')->default('en cours de traitement');
            $table->string('matricule_admin')->nullable();
            $table->string('matricule_super_admin')->nullable();
            $table->string('code_requete')->unique();
            $table->string('respect_du_modele')->nullable();
            $table->string('insertion_liste_de_presence')->nullable();
            $table->string('validite_casiers_judiciaires')->nullable();
            $table->string('verification_membres_presidium')->nullable();
            $table->string('couverture_regionale')->nullable();
            $table->dateTime('date_de_creation')->nullable();
            $table->string('statut_association')->default("inactive");
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
        Schema::dropIfExists('recording_requests');
    }
};

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
        Schema::create('representation_mandates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mandat')->nullable();
            $table->string('journal')->nullable();
            $table->string('rapport_activites')->nullable();
            $table->string('adresse_benin')->nullable();
            $table->string('adresse_etranger')->nullable();
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
        Schema::dropIfExists('representation_mandates');
    }
};

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
        Schema::disableForeignKeyConstraints();
        Schema::create('global_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('demande_enregistrement')->nullable();
            $table->string('proces_verbal')->nullable();
            $table->string('membres_ag')->nullable();
            $table->string('reglement_interieur')->nullable();
            $table->string('recepisse_de_versement')->nullable();
            $table->string('recepisse_admin')->nullable();
            $table->string('ancien_recepisse_admin')->nullable();
            $table->string('statuts')->nullable();
            $table->string('code_requete');
            $table->foreignId('recording_request_id')
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('global_attachments');
    }
};

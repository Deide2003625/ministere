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
        Schema::create('multiple_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('file');
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
        Schema::dropIfExists('multiple_files');
    }
};

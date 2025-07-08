<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('political_party', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('declaration')->nullable();
            $table->string('presence_list')->nullable();
            $table->string('founders_list')->nullable();
            $table->string('society_projects')->nullable();
            $table->string('description_sheet')->nullable();
            $table->string('logo')->nullable();
            $table->string('emblem')->nullable();
            $table->string('request_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('political_party');
    }
}

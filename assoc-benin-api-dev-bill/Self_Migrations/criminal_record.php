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
        Schema::create('criminal_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('record')->nullable();
            $table->string('recorded_role')->nullable();
            $table->string('record_age')->nullable();
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
        Schema::dropIfExists('criminal_record');
    }
}

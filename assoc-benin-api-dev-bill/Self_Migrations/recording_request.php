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
        Schema::create('recording_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('verbal_trial')->nullable();
            $table->string('legal_status')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('rules_of_procedure')->nullable();
            $table->string('address')->nullable();
            $table->string('receipt')->nullable();
            $table->string('goals')->nullable();
            $table->string('arrival_to_misp')->nullable();
            $table->string('arrival_to_daic')->nullable();
            $table->string('observations')->nullable();
            $table->string('request_status')->nullable();
            // $table->string('admin_reg_number')->nullable();
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
        Schema::dropIfExists('recording_request');
    }
}

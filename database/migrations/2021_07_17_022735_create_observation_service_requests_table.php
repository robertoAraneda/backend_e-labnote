<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_service_requests', function (Blueprint $table) {
            $table->id();
            $table->text('clinical_information');
            $table->string('name');
            $table->unsignedBigInteger('container_id');
            $table->unsignedBigInteger('specimen_id');
            $table->unsignedBigInteger('availability_id');
            $table->unsignedBigInteger('laboratory_id');
            $table->string('loinc_num');
            $table->unsignedBigInteger('analyte_id');
            $table->unsignedBigInteger('workarea_id');
            $table->unsignedBigInteger('process_time_id');
            $table->unsignedBigInteger('medical_request_type_id');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('created_user_ip', 15)->nullable();
            $table->string('updated_user_ip', 15)->nullable();
            $table->string('deleted_user_ip', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('observation_service_requests');
    }
}

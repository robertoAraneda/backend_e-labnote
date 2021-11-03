<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationObservationServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_observation_service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('lis_name');
            $table->unsignedBigInteger('observation_service_request_id');
            $table->string('model');
            $table->string('model_id');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('integration_observation_service_requests');
    }
}

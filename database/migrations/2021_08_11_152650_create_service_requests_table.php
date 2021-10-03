<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replace_service_request_id')->nullable();
            $table->string('requisition');
            $table->string('date_requisition_fragment');
            $table->integer('correlative_number');
            $table->unsignedBigInteger('service_request_status_id');
            $table->unsignedBigInteger('service_request_intent_id');
            $table->unsignedBigInteger('service_request_priority_id');
            $table->unsignedBigInteger('service_request_category_id');
            $table->unsignedBigInteger('patient_id');
            $table->timestamp('occurrence');
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->unsignedBigInteger('performer_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('authored_on');
            $table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('created_user_ip', 15)->nullable();
            $table->string('updated_user_ip', 15)->nullable();
            $table->string('deleted_user_ip', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cities', function (Blueprint $table) {
            $table->string('name');
            $table->string('code');
            $table->string('state_code');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('created_user_ip', 15)->nullable();
            $table->string('updated_user_ip', 15)->nullable();
            $table->string('deleted_user_ip', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();

          //  $table->primary('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
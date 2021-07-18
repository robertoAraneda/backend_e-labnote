<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFonasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fonasas', function (Blueprint $table) {
            $table->string('mai_code', 15);
            $table->string('rem_code', 15);
            $table->string('name', 400);
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('created_user_ip')->nullable();
            $table->string('updated_user_ip')->nullable();
            $table->string('deleted_user_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();
           // $table->primary('mai_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fonasas');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('icon');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('permission_id');
            $table->integer('order');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('created_user_ip')->nullable();
            $table->string('updated_user_ip')->nullable();
            $table->string('deleted_user_ip')->nullable();
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
        Schema::dropIfExists('menus');
    }
}

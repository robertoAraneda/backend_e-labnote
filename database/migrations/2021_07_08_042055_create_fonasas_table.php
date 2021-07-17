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
            $table->timestamps();
            $table->softDeletes();
            $table->primary('mai_code');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnalyteSamplingConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analyte_sampling_condition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analyte_id');
            $table->unsignedBigInteger('sampling_condition_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('analyte_sampling_condition');
    }
}

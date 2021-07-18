<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoincsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loinc', function (Blueprint $table) {
            $table->string('loinc_num', 10);
            $table->string('component', 255)->nullable();
            $table->string('property', 255)->nullable();
            $table->string('time_aspct', 255)->nullable();
            $table->string('system', 255)->nullable();
            $table->string('scale_typ', 255)->nullable();
            $table->string('class', 255)->nullable();
            $table->string('version_last_changed', 255)->nullable();
            $table->string('chng_type', 255)->nullable();
            $table->text('definition_description')->nullable();
            $table->string('status', 255)->nullable();
            $table->string('consumer_name', 255)->nullable();
            $table->integer('class_type')->nullable();
            $table->text('formula')->nullable();
            $table->text('exmpl_answers')->nullable();
            $table->text('survey_quest_text')->nullable();
            $table->string('survey_quest_src', 50)->nullable();
            $table->string('units_required', 1)->nullable();
            $table->string('submitted_units', 30)->nullable();
            $table->text('related_names2')->nullable();
            $table->string('short_name', 255)->nullable();
            $table->string('order_obs', 15)->nullable();
            $table->string('cdisc_common_tests', 1)->nullable();
            $table->string('hl7_field_subfield_id', 50)->nullable();
            $table->text('external_copyright_notice')->nullable();
            $table->string('example_units', 255)->nullable();
            $table->string('long_common_name', 255)->nullable();
            $table->text('units_and_range')->nullable();
            $table->string('example_ucum_units', 255)->nullable();
            $table->string('example_si_ucum_units', 255)->nullable();
            $table->string('status_reason', 9)->nullable();
            $table->text('status_text')->nullable();
            $table->text('change_reason_public')->nullable();
            $table->integer('common_test_rank')->nullable();
            $table->integer('common_order_rank')->nullable();
            $table->integer('common_si_test_rank')->nullable();
            $table->string('hl7_attachment_structure', 15)->nullable();
            $table->string('external_copyright_link', 255)->nullable();
            $table->string('panel_type', 50)->nullable();
            $table->string('ask_at_order_entry', 255)->nullable();
            $table->string('associated_observations', 255)->nullable();
            $table->string('version_first_released', 255)->nullable();
            $table->string('valid_HL7_attachment_request', 50)->nullable();
            $table->string('display_name', 255)->nullable();
            $table->timestamps();
            $table->primary('loinc_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loinc');
    }
}

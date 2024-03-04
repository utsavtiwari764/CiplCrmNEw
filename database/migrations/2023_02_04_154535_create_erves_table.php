<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erves', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->date('requisition_date');
            $table->string('project_name');
            $table->string('ref_no');
            $table->longText('required_position_details');
            $table->string('erf_type');           
            $table->date('target_date');
            $table->string('level');
            $table->string('project_manager_name');
            $table->string('team_lead');
            $table->integer('total_positions');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('job_categories')->onDelete('cascade');
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('job_locations')->onDelete('cascade');
            $table->unsignedBigInteger('jobtype_id');
            $table->foreign('jobtype_id')->references('id')->on('job_types')->onDelete('cascade');
            $table->string('positionbudget');
            $table->text('reasonfor');
            $table->string('replacement')->default('no');
            $table->string('status');
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
        Schema::dropIfExists('erves');
    }
};

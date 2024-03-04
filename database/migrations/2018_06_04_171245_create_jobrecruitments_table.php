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
        Schema::create('jobrecruitments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('job_id')->nullable();
            $table->foreign('job_id')->references('id')->on('jobs')->onUpdate('cascade')->onDelete('cascade');            
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('job_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('qualification_id')->nullable();
            $table->foreign('qualification_id')->references('id')->on('qualifications')->onUpdate('cascade')->onDelete('cascade');
            $table->string('degination')->nullable();
            $table->string('level')->nullable(); 
            $table->string('project_manager')->nullable();
            $table->string('reporting_team')->nullable();
            $table->string('position_budgeted')->nullable();
            $table->string('relevent_exp')->nullable();
            $table->string('responsibility')->nullable();
            $table->string('prerequisite')->nullable();
            $table->unsignedBigInteger('anycertification_id')->nullable();
            $table->foreign('anycertification_id')->references('id')->on('certifications')->onUpdate('cascade')->onDelete('cascade');
            $table->string('scopeofwork')->nullable();
            $table->string('user_id')->nullable();
            $table->string('assign_id')->nullable();
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
        Schema::dropIfExists('jobrecruitments');
    }
};

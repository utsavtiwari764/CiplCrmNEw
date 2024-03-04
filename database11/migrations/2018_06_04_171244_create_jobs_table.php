<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('pid')->nullable();
            $table->text('erf_id')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
            $table->longText('job_requirement')->nullable();
            $table->string('recruitment_type')->nullable();
            $table->string('recruitment')->nullable();
            $table->string('approved')->nullable();
            $table->string('billable_type')->nullable();
            $table->string('project_name')->nullable();
            $table->string('work_experience')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}

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
        Schema::create('onlineexam_job_application_results', function (Blueprint $table) {
           
            $table->id();
            $table->integer('onlinexamjobsapp_id');
          //  $table->foreign('onlinexamjobsapp_id')->references('id')->on('onlinexam_job_applications')->onUpdate('cascade')->onDelete('cascade');
          
            $table->integer('onlinequestion_id');
           // $table->foreign('onlinequestion_id')->references('id')->on('onlinequestions')->onUpdate('cascade')->onDelete('cascade');
            $table->text('answer_by_jobapplication')->nullable();
            $table->string('marks')->nullable();
            $table->string('status')->default('0');
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
        Schema::dropIfExists('onlineexam_job_application_results');
    }
};

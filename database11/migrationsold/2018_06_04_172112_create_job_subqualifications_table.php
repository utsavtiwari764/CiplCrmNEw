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
        Schema::create('job_subqualifications', function (Blueprint $table) {
            $table->increments('id'); 
              $table->unsignedBigInteger('subqualification_id')->nullable();
               $table->foreign('subqualification_id')->references('id')->on('subqualifications')->onUpdate('cascade')->onDelete('cascade');
               $table->integer('jobrecruitment_id')->unsigned();
               $table->foreign('jobrecruitment_id')->references('id')->on('jobrecruitments')->onUpdate('cascade')->onDelete('cascade');
              
               $table->unsignedBigInteger('job_id')->nullable();
               $table->foreign('job_id')->references('id')->on('jobs')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('job_subqualifications');
    }
};

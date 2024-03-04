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
        Schema::create('job_applicationsubqualifications', function (Blueprint $table) {
                 $table->increments('id');
         
                $table->integer('jobapplication_id')->unsigned();
                $table->foreign('jobapplication_id')->references('id')->on('job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedBigInteger('subqualifications_id');
                $table->foreign('subqualifications_id')->references('id')->on('subqualifications')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('job_applicationsubqualifications');
    }
};

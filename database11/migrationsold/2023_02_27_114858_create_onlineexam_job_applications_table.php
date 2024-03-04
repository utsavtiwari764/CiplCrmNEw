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
        Schema::create('onlineexam_job_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('onlinexam_id');
            $table->foreign('onlinexam_id')->references('id')->on('onlinexams')->onUpdate('cascade')->onDelete('cascade');
          
            $table->integer('jobapplication_id')->unsigned();
            $table->foreign('jobapplication_id')->references('id')->on('job_applications')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('onlineexam_job_applications');
    }
};

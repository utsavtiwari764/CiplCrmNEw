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
        Schema::create('onlinexam_job_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('jobapplication_id')->nullable();
            $table->bigInteger('onlinexam_id')->unsigned()->nullable();
            //$table->foreign('jobapplication_id')->references('id')->on('job_applications')
                 //   ->onDelete('cascade'); 
            $table->tinyInteger('in_attempt')->default(0);
            $table->foreign('onlinexam_id')->references('id')->on('onlinexams')
                  ->onDelete('cascade');
            $table->string('status')->default(1);
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
        Schema::dropIfExists('onlinexam_job_applications');
    }
};

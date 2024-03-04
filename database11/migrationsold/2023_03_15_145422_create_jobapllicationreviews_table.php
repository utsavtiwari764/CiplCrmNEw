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
        Schema::create('jobapllicationreviews', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('rating')->nullable();
            $table->text('comment')->nullable()->default(null);
            
            $table->string('interview_type')->nullable();
            $table->string('status')->nullable();
            $table->integer('interview_schedule_id')->unsigned();

            $table->foreign('interview_schedule_id')->references('id')->on('interview_schedules')
                ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('jobapllicationreviews');
    }
};

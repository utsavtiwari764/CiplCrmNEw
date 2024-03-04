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
        Schema::create('onlinequestions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->text('answer')->nullable();
            $table->string('marks');
            $table->enum('question_type',['optional', 'description']);
            $table->integer('jobcategory_id')->unsigned()->nullable();
            $table->foreign('jobcategory_id')->references('id')->on('job_categories')->onDelete('cascade');
            $table->enum('status',['enable', 'disable'])->default('enable');
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
        Schema::dropIfExists('onlinequestions');
    }
};

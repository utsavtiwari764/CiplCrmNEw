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
        Schema::create('subqualifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('qualification_id')->unsigned();
           
            $table->foreign('qualification_id')->references('id')->on('qualifications')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('subqualifications');
    }
};

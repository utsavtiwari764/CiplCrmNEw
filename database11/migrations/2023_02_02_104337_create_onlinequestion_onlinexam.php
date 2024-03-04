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
        Schema::create('onlinequestion_onlinexam', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onlinequestion_id');
            $table->unsignedBigInteger('onlinexam_id');
            $table->string('marks')->nullable();
            $table->foreign('onlinequestion_id')->references('id')->on('onlinequestions')
                    ->onDelete('cascade'); 
            $table->foreign('onlinexam_id')->references('id')->on('onlinexams')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('onlinequestion_onlinexam');
    }
};

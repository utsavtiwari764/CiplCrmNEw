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
        Schema::create('onlinequestions_onlinexams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onlinequestions_id');
            $table->unsignedBigInteger('onlinexams_id');
            $table->foreign('onlinequestions_id')->references('id')->on('onlinequestions')
                    ->onDelete('cascade'); 
            $table->foreign('onlinexams_id')->references('id')->on('onlinexams')
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
        Schema::dropIfExists('onlinequestions_onlinexams');
    }
};

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateApplicationStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->timestamps();
        });

        $data = [
            ['status' => 'applied'],
            ['status' => 'online exam'],
            ['status' => 'interview round 1'],
            ['status' => 'interview round 2'],
            ['status' => 'hired'],
            ['status' => 'rejected'],
            ['status' => 'expired'],
            ['status' => 'pass'],
            ['status' => 'failed'],
            ['status' => 'Salary Negotiation'],
            ['status' => 'assign round 1'],
            ['status' => 'assign round 2']
        ];

        DB::table('application_status')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_status');
    }
}

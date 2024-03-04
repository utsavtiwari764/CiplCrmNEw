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
        Schema::create('salarycreations', function (Blueprint $table) {
            $table->id();
            $table->integer('job_application_id')->unsigned();
            $table->foreign('job_application_id')->references('id')->on('job_applications')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('employeeCode')->nullable();
            $table->text('location')->nullable();
            $table->string('salaryType')->nullable();
            $table->string('name');            
            $table->string('state')->nullable();
            $table->string('designation')->nullable();
            $table->string('effectiveDate')->nullable();
            $table->string('dateOfJoining')->nullable();
            $table->string('ctc')->nullable();
            $table->string('basicMonthly')->nullable();
            $table->string('basicAnnual')->nullable();
            $table->string('hrmMonthly')->nullable();
            $table->string('hrmAnnual')->nullable();
            $table->string('specialMonthly')->nullable();
            $table->string('spciealAnnual')->nullable();
            $table->string('pfMonthly')->nullable();
            $table->string('pTaxMonthly')->nullable();
            $table->string('totalDeductionAnnually')->nullable();
            $table->string('pfEMonthly')->nullable();
            $table->string('pfEAnnually')->nullable();
            $table->string('pfAAnnually')->nullable();
            $table->string('eStateInsuranceMonthly')->nullable();
            $table->string('eStateInsuranceAnnually')->nullable();
            $table->string('gratuityMonthly')->nullable();
            $table->string('gratuityAnnually')->nullable();
            $table->string('ltaMonthly')->nullable();
            $table->string('ltaAnnually')->nullable();
            $table->string('insuranceMonthly')->nullable();
            $table->string('insuranceAnnually')->nullable();
            $table->string('fixedCtcMonthly')->nullable();
            $table->string('fixedCtcAnnually')->nullable();
            $table->string('totalCTC')->nullable();
            $table->string('netTakeHome')->nullable();
            $table->string('grossAmount')->nullable();
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
        Schema::dropIfExists('salarycreations');
    }
};

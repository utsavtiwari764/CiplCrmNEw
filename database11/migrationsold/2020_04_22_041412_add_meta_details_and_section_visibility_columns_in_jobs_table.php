<?php

use App\ApplicationSetting;
use App\Job;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddMetaDetailsAndSectionVisibilityColumnsInJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->text('meta_details')->after('required_columns');
            $table->text('section_visibility')->after('meta_details');
        });

        $setting = ApplicationSetting::select('id')->first();

        $jobs = Job::select('id', 'title', 'meta_details')->get();
        foreach ($jobs as $job) {
            $job->meta_details = [
                'title' => $job->title,
                  ];

            $job->section_visibility = [
                'profile_image' =>'yes',
                'resume' => 'yes',
                'cover_letter' => 'yes',
                'terms_and_conditions' => 'yes'
            ];

            $job->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('meta_details');
            $table->dropColumn('section_visibility');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageThemeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('home_background_image')->nullable()->default(null)->after('front_custom_css');
            $table->string('welcome_title')->nullable()->default(null)->after('home_background_image');
            $table->text('welcome_sub_title')->nullable()->default(null)->after('welcome_title');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn('home_background_image');
            $table->dropColumn('welcome_title');
            $table->dropColumn('welcome_sub_title');
        });
    }
}

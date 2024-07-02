<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('slider_id');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });

        Schema::create('home_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('home_id');
            $table->mediumText('description')->nullable();
            $table->string('language', 2);
            $table->timestamps();
        });

        Schema::create('home_parts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique();
            $table->string('type');
            $table->string('section_type')->nullable(true);
            $table->json('content');
            $table->timestamps();
        });

        Schema::create('h_section_home', function (Blueprint $table) {
            $table->unsignedInteger('home_id');
            $table->unsignedInteger('h_section_id');
        });

        /////sections
        Schema::create('h_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['manual', 'latest']);
            $table->timestamps();
        });

        Schema::create('h_section_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('h_section_id');
            $table->string('title');
            $table->string('language', 2);
            $table->timestamps();
        });

        Schema::create('h_section_product', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('h_section_id');
            $table->unsignedMediumInteger('arrange')->default(1);
        });

        ///slider
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('slider_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('slider_id')->nullable(true);
            $table->string('path');
            $table->string('mobile_path');
            $table->string('link');
            $table->string('language', 2);
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
        Schema::dropIfExists('homes');
        Schema::dropIfExists('home_translations');
        Schema::dropIfExists('home_parts');
        Schema::dropIfExists('h_section_home');
        Schema::dropIfExists('h_sections');
        Schema::dropIfExists('h_section_translations');
        Schema::dropIfExists('h_section_product');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('slider_images');
    }
}

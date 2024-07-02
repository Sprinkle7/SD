<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('level');
            $table->integer('arrange')->default(1);
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable(true);
            $table->string('thumbnail_image')->nullable(true);
            $table->timestamps();
        });

        Schema::create('menu_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id');
            $table->string('title', 50);
            $table->string('slug', 70);
            $table->longText('description')->nullable();
            $table->longText('lower_description')->nullable();
            $table->string('language', 2);
            $table->timestamps();
        });

        Schema::create('menu_cover_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id')->nullable(true);
            $table->string('language', 2);
            $table->string('path');
            $table->string('mobile_path');
            $table->string('link');
            $table->timestamps();
        });

        Schema::create('menu_product', function (Blueprint $table) {
            $table->bigInteger('menu_id');
            $table->bigInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('menu_translations');
        Schema::dropIfExists('menu_cover_images');
    }
}

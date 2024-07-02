<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });

        Schema::create('option_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id');
            $table->string('title');
            $table->string('language', 2);
            $table->timestamps();
        });

        Schema::create('option_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id');
            $table->timestamps();
        });

        Schema::create('option_value_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('option_value_id');
            $table->string('title');
            $table->string('language', 2);
            $table->timestamps();
        });

        /**
         *  product option
         **/

        Schema::create('option_product', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('option_id');
            $table->boolean('has_no_select')->default(0);
            $table->unsignedSmallInteger('arrange');
        });

        Schema::create('option_product_translation', function (Blueprint $table) {
            $table->bigInteger('product_id');
            $table->bigInteger('option_id');
            $table->string('title', 50);
            $table->string('language', 5);
        });


        Schema::create('option_value_product', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('option_value_id');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->nullable(true);
            $table->unsignedSmallInteger('arrange')->default(1);
        });

        Schema::create('excluded_option_values', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('exclude');
            $table->unsignedInteger('from');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
        Schema::dropIfExists('option_translations');
        Schema::dropIfExists('option_values');
        Schema::dropIfExists('option_value_translations');
    }
}

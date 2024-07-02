<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combinations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('combination',50);
            $table->decimal('price', 10, 2)->nullable(false);
            $table->decimal('real_price', 10, 2)->nullable(false);
            $table->decimal('additional_price', 8, 2)->default(0);
            $table->boolean('is_active')->default(0);
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });

        Schema::create('combination_option_value', function (Blueprint $table) {
            $table->unsignedBigInteger('combination_id');
            $table->unsignedBigInteger('option_value_id');
        });

        Schema::create('combination_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('combination_id')->nullable(true);
            $table->string('path');
            $table->unsignedMediumInteger('arrange')->default(1);
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
        Schema::dropIfExists('combinations');
    }
}

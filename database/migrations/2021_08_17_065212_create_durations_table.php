<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('durations', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('duration');
            $table->timestamps();
        });

        Schema::create('duration_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('duration_id');
            $table->string('title');
            $table->string('language');
            $table->timestamps();
        });

        Schema::create('duration_product', function (Blueprint $table) {
            $table->bigInteger('product_id');
            $table->bigInteger('duration_id');
            $table->unsignedSmallInteger('price')->default(0);
            $table->unsignedInteger('default_value')->default(1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('durations');
        Schema::dropIfExists('duration_translations');
    }
}

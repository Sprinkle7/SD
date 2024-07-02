<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_id');
            $table->string('title', 50);
            $table->string('image')->nullable();
            $table->unsignedMediumInteger('height')->nullable();
            $table->unsignedMediumInteger('width')->nullable();
            $table->string('language', 3);
            $table->timestamps();
        });

        Schema::create('service_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_id');
            $table->decimal('price', 10, 2);
            $table->unsignedMediumInteger('duration');
            $table->timestamps();
        });

        Schema::create('service_value_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('service_value_id');
            $table->string('title', 50);
            $table->string('language', 3);
            $table->timestamps();
        });

        Schema::create('product_service', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('service_id');
            $table->boolean('has_no_select');
        });

        Schema::create('excluded_services', function (Blueprint $table) {
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
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_translations');
        Schema::dropIfExists('service_values');
        Schema::dropIfExists('service_value_translations');
        Schema::dropIfExists('product_service');


    }
}

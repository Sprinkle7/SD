<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('customs_price')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('has_ust_id')->default(0);
            $table->boolean('tax_required')->default(1);
            $table->string('code');
            $table->timestamps();
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('country_id');
            $table->string('name', 50);
            $table->string('info', 191)->nullable();
            $table->string('language', 2);
            $table->timestamps();
        });

        Schema::create('country_post_duration', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('post_id');
            $table->decimal('min_price', 9, 2);
            $table->decimal('price', 9, 2);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('country_translations');
        Schema::dropIfExists('country_post_duration');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_infos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('technical_info_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('technical_info_id');
            $table->string('title', 50);
            $table->mediumText('description');
            $table->string('language');
            $table->timestamps();
        });

        Schema::create('product_technical_info', function (Blueprint $table) {
            $table->bigInteger('product_id');
            $table->bigInteger('technical_info_id');
            $table->unsignedInteger('arrange')->default(1);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_infos');
        Schema::dropIfExists('technical_info_translations');
    }
}

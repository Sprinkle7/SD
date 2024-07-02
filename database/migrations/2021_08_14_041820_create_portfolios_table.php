<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->timestamps();
        });

        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('portfolio_id')->nullable(true);
            $table->string('path');
            $table->unsignedSmallInteger('arrange')->default(1);
            $table->timestamps();
        });

//        Schema::create('portfolio_product', function (Blueprint $table) {
//            $table->unsignedInteger('portfolio_id');
//            $table->unsignedBigInteger('product_id');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('portfolio_images');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 25)->nullable(true);
            $table->decimal('price', 10, 2)->nullable(true);
            $table->boolean('reorder')->default(0);
            $table->string('cover_image')->nullable(true);;
            $table->string('video')->nullable(true);
            $table->string('data_sheet_pdf')->nullable(true);
            $table->string('assembly_pdf')->nullable(true);
            $table->string('zip')->nullable(true);
            $table->boolean('is_active')->default(0);
            $table->unsignedInteger('portfolio_id')->nullable(true);
            $table->unsignedBigInteger('default_menu_id')->nullable(true);
            $table->unsignedBigInteger('default_combination_id')->nullable(true);
            $table->timestamps();
        });

        Schema::create('product_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('title');
            $table->string('slug');
            $table->mediumText('benefit_desc')->nullable(true);;
            $table->mediumText('item_desc')->nullable(true);;
            $table->mediumText('feature_desc')->nullable(true);;
            $table->string('language');
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_translations');
    }
}

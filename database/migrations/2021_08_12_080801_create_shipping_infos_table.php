<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_infos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('shipping_info_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('shipping_info_id');
            $table->string('title', 50);
            $table->mediumText('description');
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
        Schema::dropIfExists('shipping_infos');
        Schema::dropIfExists('shipping_info_translations');
    }
}

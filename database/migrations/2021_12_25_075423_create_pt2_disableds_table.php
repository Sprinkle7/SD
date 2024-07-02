<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePt2DisabledsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pt2_disableds', function (Blueprint $table) {
            $table->unsignedBigInteger('pt2_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('pt1_combination_id');
            $table->unsignedBigInteger('disabled_category_id');
            $table->unsignedBigInteger('disabled_pt1_combination_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pt2_disableds');
    }
}

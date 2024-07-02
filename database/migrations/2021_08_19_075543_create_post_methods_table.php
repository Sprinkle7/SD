<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_methods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('post_method_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_method_id');
            $table->string('title', 70);
            $table->string('language', 5);
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
        Schema::dropIfExists('post_methods');
        Schema::dropIfExists('post_method_translations');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('title', 30)->unique();
            $table->boolean('default')->default(0);
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

//        Schema::create('language_references', function (Blueprint $table) {
//            $table->id();
//            $table->string('code', 2)->unique();
//            $table->string('title', 30)->unique();
//            $table->timestamps();
//        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}

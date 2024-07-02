<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('contact_us_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_us_id');
            $table->mediumText('description');
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
        Schema::dropIfExists('contact_us');
        Schema::dropIfExists('contact_us_translations');
    }
}

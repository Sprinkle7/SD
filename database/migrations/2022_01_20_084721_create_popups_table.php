<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });

        Schema::create('popup_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('popup_id');
            $table->string('title');
            $table->mediumText('content');
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
        Schema::dropIfExists('popups');
        Schema::dropIfExists('popup_translations');
    }
}

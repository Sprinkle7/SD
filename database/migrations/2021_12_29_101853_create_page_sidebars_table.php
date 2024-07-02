<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageSidebarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_sidebar_infos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('page_sidebars_items', function (Blueprint $table) {
            $table->unsignedInteger('sidebar_id');
            $table->unsignedInteger('page_id');
            $table->enum('type', ['page'])->defualt('page');
            $table->unsignedMediumInteger('arrange');
            $table->primary(['sidebar_id', 'page_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_sidebar_infos');
        Schema::dropIfExists('page_sidebars');
    }
}

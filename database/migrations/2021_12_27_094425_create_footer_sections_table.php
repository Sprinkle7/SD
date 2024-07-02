<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFooterSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footer_sections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('footer_section_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('footer_section_id');
            $table->enum('type', ['page', 'menu'])->nullable();
            $table->string('title')->nullable();
            $table->string('language', 5);
            $table->unsignedMediumInteger('arrange');
            $table->timestamps();
        });
        Schema::create('footer_section_page', function (Blueprint $table) {
            $table->unsignedInteger('footer_section_id');
            $table->unsignedInteger('page_id');
            $table->unsignedMediumInteger('arrange');

        });

        Schema::create('footer_section_menu', function (Blueprint $table) {
            $table->unsignedInteger('footer_section_id');
            $table->unsignedInteger('menu_id');
            $table->unsignedMediumInteger('arrange');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('footer_sections');
        Schema::dropIfExists('footer_section_translations');
        Schema::dropIfExists('footer_section_page');
        Schema::dropIfExists('footer_section_menu');
    }
}

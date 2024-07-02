<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('combination_id');
            $table->unsignedBigInteger('duration_id');
            $table->mediumInteger('quantity')->default(1);
            $table->string('services')->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('session_id')->nullable(true);
            $table->primary(['user_id','product_id','combination_id','duration_id']);
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
        Schema::dropIfExists('carts');
    }
}

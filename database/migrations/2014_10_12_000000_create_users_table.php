<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(true);
            $table->string('email')->unique(false)->unique();
            $table->string('phone')->nullable(true)->unique();
            $table->enum('gender', ['male', 'female'])->nullable(true);
            $table->string('password')->nullable(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('company')->nullable(true);
            $table->string('address')->nullable(true);
            $table->string('additional_address')->nullable(true);
            $table->unsignedInteger('postcode')->nullable(true);
            $table->string('city')->nullable(true);
            $table->bigInteger('country_id')->nullable(true);
            $table->integer('role_id')->nullable(false);
            $table->boolean('confirmed')->default(0);
            $table->boolean('profile_completed')->default(0);
            $table->rememberToken();
            $table->timestamps();
            \Illuminate\Support\Facades\DB::update("ALTER TABLE users AUTO_INCREMENT = 5000;");

        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('role_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->string('title');
            $table->string('language', 2);
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_translations');
    }
}

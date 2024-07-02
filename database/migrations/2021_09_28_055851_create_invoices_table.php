<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('	invoice_id ');
            $table->string('payment_intent')->nullable(false)->unique();
            $table->enum('payment_type', ['stripe', 'paypal', 'prePaid', 'postPaid']);
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount_total', 10, 2);
            $table->boolean('is_complete')->default(0);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->string('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->boolean('has_ust_id')->default(0);
            $table->string('ust_id', 10)->nullable(true);
            $table->boolean('tax_required')->default(0);
            $table->string('coupon_code')->nullable();
            $table->unsignedSmallInteger('coupon_percent')->nullable();
            $table->timestamp('coupon_expires_at')->nullable();
            $table->boolean('seen')->default(0);
            $table->boolean('confirmed_email_has_sent')->default(0);
            $table->timestamp('confirmed_email_sent_at')->nullable();
            $table->enum('state', ['new_order', 'canceled', 'confirmed', 'ready', 'updating', 'completed'])->default('new _order');
            $table->string('file')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            \Illuminate\Support\Facades\DB::update("ALTER TABLE invoices AUTO_INCREMENT = 10000;");
        });

        Schema::create('invoice_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('order_address_id')->unique();
            $table->string('payment_intent');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('address_id')->nullable(true);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->text('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->unsignedInteger('customs_percent');
            $table->decimal('customs_price', 10, 2);
            $table->unsignedBigInteger('post_id');
            $table->decimal('min_items_total_price', 10, 2);
            $table->decimal('post_price', 10, 2);
            $table->decimal('items_total_net_price', 10, 2);
            $table->enum('status', ['inprogress', 'shipped']);
            $table->string('xml_invoice')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('invoice_address_products', function (Blueprint $table) {
            $table->bigIncrements('invoice_address_product_id');
            $table->string('order_address_id');
            $table->string('payment_intent');
            $table->unsignedBigInteger('user_id');
            $table->smallInteger('tax')->nullable(true);
            $table->decimal('tax_price', 10, 2);
            $table->unsignedBigInteger('product_id');
            $table->string('product_title', 191);
            $table->decimal('product_price', 10, 2);
            $table->unsignedBigInteger('combination_id');
            $table->decimal('combination_price', 10, 2);
            $table->decimal('combination_additional_price', 10, 2);
            $table->unsignedBigInteger('duration_id');
            $table->mediumInteger('duration');
            $table->unsignedSmallInteger('duration_percent');
            $table->decimal('duration_price', 10, 2);
            $table->string('services', 100)->nullable();
            $table->string('services_total_price')->nullable();
            $table->json('services_data')->nullable();
            $table->mediumInteger('quantity')->default(1);
            $table->unsignedMediumInteger('discount_quantity');
            $table->unsignedSmallInteger('discount_percent');
            $table->decimal('discount_price', 10, 2);
            $table->unsignedMediumInteger('pre_paid_percent');
            $table->decimal('pre_paid_coupon_price', 10, 2);
            $table->unsignedMediumInteger('customs_percent');
            $table->unsignedSmallInteger('customs_price');
            $table->decimal('list_price', 10, 2);
            $table->decimal('net_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->unsignedMediumInteger('number_of_images')->default(0);
            $table->boolean('is_available')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('invoice_address_product_translations', function (Blueprint $table) {
            $table->unsignedInteger('invoice_address_product_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_title');
            $table->unsignedBigInteger('combination_id');
            $table->json('options');
            $table->string('language', 5);
            $table->primary(['invoice_address_product_id', 'language']);
        });

        Schema::create('invoice_product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_address_product_id');
            $table->string('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_addresses');
        Schema::dropIfExists('invoice_address_products');
        Schema::dropIfExists('invoice_product_images');
        Schema::dropIfExists('invoice_address_product_translations');
    }
}

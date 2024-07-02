<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('session_id')->nullable(false)->unique();
            $table->string('payment_intent')->nullable(false)->unique();
            $table->enum('payment_type', ['stripe', 'paypal','','postPaid']);
            $table->unsignedBigInteger('user_id');
            $table->boolean('has_ust_id')->default(0);
            $table->boolean('tax_required')->default(0);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->string('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->string('ust_id', 10)->nullable(true);
            $table->timestamp('expires_at');
            $table->decimal('amount_total', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->unsignedSmallInteger('coupon_percent')->nullable();
            $table->timestamp('coupon_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->string('order_address_id')->unique();
            $table->string('session_id');
            $table->unsignedBigInteger('address_id')->nullable(true);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->text('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->unsignedSmallInteger('customs_percent')->nullable();
            $table->unsignedDecimal('customs_price', 10, 2)->default(0);
            $table->unsignedBigInteger('post_id');
            $table->unsignedDecimal('min_items_total_price', 10, 2);
            $table->unsignedDecimal('post_price', 10, 2);
            $table->unsignedDecimal('items_total_net_price', 10, 2);
        });

        Schema::create('order_address_products', function (Blueprint $table) {
            $table->string('order_address_id');
            $table->string('session_id', 80);
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
            $table->unsignedMediumInteger('pre_paid_percent');
            $table->decimal('pre_paid_coupon_price',10,2);
            $table->unsignedMediumInteger('customs_percent');
            $table->unsignedSmallInteger('customs_price');
            $table->decimal('discount_price', 10, 2);
            $table->decimal('list_price', 10, 2);
            $table->decimal('net_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->primary(['session_id', 'product_id', 'combination_id', 'duration_id', 'services'], 'or_ad_p_s_p_c');
        });

        Schema::create('failed_orders', function (Blueprint $table) {
            $table->string('session_id')->nullable(false)->unique();
            $table->string('payment_intent')->nullable(false)->unique();
            $table->enum('payment_type', ['stripe', 'paypal','prePaid','postPaid']);
            $table->unsignedBigInteger('user_id');
            $table->boolean('has_ust_id')->default(0);
            $table->boolean('tax_required')->default(0);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->string('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->string('ust_id', 10)->nullable(true);
            $table->timestamp('expires_at');
            $table->decimal('amount_total', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->unsignedSmallInteger('coupon_percent')->nullable();
            $table->timestamp('coupon_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('failed_order_addresses', function (Blueprint $table) {
            $table->string('order_address_id')->unique();
            $table->string('session_id');
            $table->unsignedBigInteger('address_id')->nullable(true);
            $table->unsignedBigInteger('country_id');
            $table->string('country_name', 50);
            $table->string('city');
            $table->string('address');
            $table->text('additional_address')->nullable(true);
            $table->unsignedInteger('postcode');
            $table->unsignedSmallInteger('customs_percent')->nullable();
            $table->unsignedDecimal('customs_price', 10, 2)->default(0);
            $table->unsignedBigInteger('post_id');
            $table->unsignedDecimal('min_items_total_price', 10, 2);
            $table->unsignedDecimal('post_price', 10, 2);
            $table->unsignedDecimal('items_total_net_price', 10, 2);
        });

        Schema::create('failed_order_address_products', function (Blueprint $table) {
            $table->string('order_address_id');
            $table->string('session_id', 80);
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
            $table->decimal('pre_paid_coupon_price',10,2);
            $table->unsignedMediumInteger('customs_percent');
            $table->unsignedSmallInteger('customs_price');
            $table->decimal('list_price', 10, 2);
            $table->decimal('net_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->primary(['session_id', 'product_id', 'combination_id', 'duration_id', 'services'], 'or_ad_p_s_p_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('failed_orders');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('order_address_products');
    }
}

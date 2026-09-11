<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->tinyInteger('product_stock_type')->default(1);
            $table->integer('product_stock')->default(10);
            $table->string('product_code', 100);
            $table->integer('reserved')->nullable();
            $table->integer('allocated')->nullable();
            $table->string('asin_code', 255)->nullable();
            $table->string('gtin_code', 255)->nullable();
            $table->tinyInteger('asin_validation')->nullable();
            $table->string('product_name', 100);
            $table->longText('product_description')->nullable();
            $table->longText('kit_description')->nullable();
            $table->string('product_barcode', 50)->nullable();
            $table->tinyInteger('product_warrantly')->default(6);
            $table->integer('bucket_img_status')->default(0);
            $table->enum('product_condition', ['new', 'shiny', 'gold', 'silver', 'bronze', 'stallone'])->default('new');
            $table->double('product_weight')->default(0);
            $table->string('dimension_LWH', 20)->nullable();
            $table->tinyInteger('portal_show')->default(1);
            $table->double('product_price')->default(0);
            $table->double('fixed_stock_price')->default(0);
            $table->double('purchased_price')->default(0);
            $table->integer('vat_id')->default(1);
            $table->tinyInteger('product_type')->default(1);
            $table->string('assembly_time', 10)->nullable();
            $table->string('HS_Code', 50)->nullable();
            $table->char('country_id', 2)->default('NL');
            $table->tinyInteger('isactive')->default(1);
            $table->smallInteger('upload_type')->default(0);
            $table->unsignedBigInteger('customer_group_id')->nullable()->index();
            $table->tinyInteger('is_thuiskopie')->default(0);
            $table->integer('stock_threshold')->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->timestamp('last_modified')->nullable();
            $table->timestamp('image_Last_modified')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_products');
    }
};
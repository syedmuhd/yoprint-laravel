<?php

use App\Models\Category;
use App\Models\Color;
use App\Models\Detail;
use App\Models\Mill;
use App\Models\Pricing;
use App\Models\Size;
use App\Models\Status;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_key')->unique()->index();
            $table->string('product_title')->nullable();
            $table->integer('qty')->default(0);
            $table->bigInteger('inventory_key')->nullable();
            $table->tinyInteger('size_index')->nullable();
            $table->string('product_measurements')->nullable();
            $table->string('gtin')->nullable();

            $table->foreignIdFor(Detail::class)->nullable();
            $table->foreignIdFor(Size::class)->nullable();
            $table->foreignIdFor(Category::class)->nullable();
            $table->foreignIdFor(Subcategory::class)->nullable();
            $table->foreignIdFor(Color::class)->nullable();
            $table->foreignIdFor(Pricing::class)->nullable();
            $table->foreignIdFor(Mill::class)->nullable();
            $table->foreignIdFor(Status::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Pricinggroup;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->string('price_text')->nullable();
            $table->decimal('suggested_price', 6, 2)->nullable();
            $table->decimal('piece_price', 6, 2)->nullable();
            $table->decimal('dozens_price', 6, 2)->nullable();
            $table->decimal('case_price', 6, 2)->nullable();
            $table->decimal('msrp', 6, 2)->nullable();
            $table->decimal('map_pricing', 6, 2)->nullable();

            $table->foreignIdFor(Pricinggroup::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};

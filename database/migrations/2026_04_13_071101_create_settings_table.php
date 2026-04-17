<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Stripe credentials
            $table->string('stripe_public_key')->nullable();
            $table->string('stripe_secret_key')->nullable();

            // Pricing
            $table->decimal('per_page_price', 8, 2)->nullable();

            // AI Config
            $table->text('ai_prompt')->nullable();
            $table->string('ai_key')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('upload_id')->nullable()->constrained()->nullOnDelete();

        $table->string('business_name');
        $table->string('file_name');

        $table->enum('status', ['analyzing', 'completed', 'failed'])->default('analyzing');
        $table->enum('payment_status', ['pending', 'paid'])->default('pending');

        $table->integer('page_count')->default(1);
        $table->decimal('price', 10, 2)->default(1.00);

        $table->decimal('income', 15, 2)->default(0);
        $table->decimal('expenses', 15, 2)->default(0);
        $table->decimal('net_income', 15, 2)->default(0);

        $table->json('income_categories')->nullable();
        $table->json('expense_categories')->nullable();
        $table->json('analysis_results')->nullable();

        $table->string('date_range')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

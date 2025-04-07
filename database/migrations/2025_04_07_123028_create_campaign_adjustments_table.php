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
        Schema::create('campaign_adjustments', function (Blueprint $table) {
            $table->id();

            // The campaign fund being adjusted
            $table->foreignId('campaign_fund_id')->constrained('funds')->onDelete('cascade');

            // Main fund (can also be stored in config or referenced by fund type = main)
            $table->foreignId('main_fund_id')->constrained('funds')->onDelete('cascade');

            // Adjustment amount
            $table->decimal('amount', 15, 2);

            // Type of adjustment
            $table->enum('type', ['to_campaign', 'to_main']);
            // 'to_campaign' = main fund sends to campaign
            // 'to_main' = campaign returns leftover to main

            // Optional notes
            $table->text('note')->nullable();

            // Who made the adjustment
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_adjustments');
    }
};

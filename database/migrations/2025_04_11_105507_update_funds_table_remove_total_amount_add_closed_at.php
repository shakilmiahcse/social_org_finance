<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table->timestamp('closed_at')->nullable()->after('updated_at'); // You can change position
        });
    }

    public function down(): void
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable(); // Adjust type if different
            $table->dropColumn('closed_at');
        });
    }
};

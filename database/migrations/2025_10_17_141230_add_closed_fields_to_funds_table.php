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

            $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
            $table->text('closed_note')->nullable()->after('closed_by');

            // If funds table is related to users table
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funds', function (Blueprint $table) {
            // Drop the new ones
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['closed_by', 'closed_note']);
        });
    }
};

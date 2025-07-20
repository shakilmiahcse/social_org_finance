<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First create the organizations table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency', 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create a default organization for existing data
        $defaultOrgId = DB::table('organizations')->insertGetId([
            'name' => 'Default Organization',
            'email' => 'default@organization.com',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Add organization_id columns without constraints first
        $tables = ['users', 'funds', 'donors', 'campaign_adjustments', 'transactions', 'transaction_attachments'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('id');
            });
        }

        // Update existing records to use the default organization
        foreach ($tables as $table) {
            DB::table($table)->update(['organization_id' => $defaultOrgId]);
        }

        // Now add the foreign key constraints
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable(false)->change();
                $table->foreign('organization_id')->references('id')->on('organizations');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First remove foreign key constraints
        $tables = ['users', 'funds', 'donors', 'campaign_adjustments', 'transactions', 'transaction_attachments'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['organization_id']);
            });
        }

        // Then remove organization_id columns
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('organization_id');
            });
        }

        // Finally drop the organizations table
        Schema::dropIfExists('organizations');
    }
};

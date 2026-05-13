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
        Schema::table('users', function (Blueprint $table) {
            // Location Engine Fields
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();

            // Contact & Privacy Toggles
            $table->string('phone_number')->nullable();
            $table->boolean('show_phone')->default(false);
            $table->boolean('show_email')->default(false);

            // Anti-Spam / Identity Gate
            $table->boolean('is_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'zip_code',
                'phone_number',
                'show_phone',
                'show_email',
                'is_verified'
            ]);
        });
    }
};

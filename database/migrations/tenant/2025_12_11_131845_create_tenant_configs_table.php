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
        Schema::create('tenant_configs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('isPasswordShouldRenew')->default(false);
            $table->unsignedInteger('passwordExpirationDelay')->default(45);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_configs');
    }
};

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
        Schema::create('tenant_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('offer_name')->nullable();
            $table->uuid('global_id');
            $table->unsignedBigInteger('used_storage')->default(0);
            $table->float('allocated_storage')->default(0);
            $table->float('allocated_users')->default(0);
            $table->string('bucket')->nullable();
            $table->boolean('is_health')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_contracts');
    }
};

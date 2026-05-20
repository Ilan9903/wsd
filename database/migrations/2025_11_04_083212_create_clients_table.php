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
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('global_id')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('front_route', 255)->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('avatar')->nullable();
            $table->string('tenant')->nullable();
            $table->string('domain')->nullable();
            $table->string('bucket')->nullable();
            $table->boolean('is_health')->default(false);
            $table->bigInteger('used_storage')->default(0);
            $table->integer('allocated_users')->default(10);
            $table->integer('allocated_storage')->default(10);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

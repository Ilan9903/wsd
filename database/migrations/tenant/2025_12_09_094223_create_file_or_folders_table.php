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
        Schema::create('file_or_folders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->nullable()->references('id')->on('file_or_folders');
            $table->foreignUuid('link_id')->nullable()->references('id')->on('links');
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->string('mime_type')->nullable();
            $table->string('name');
            $table->string('type');
            $table->bigInteger('size')->default(0);
            $table->text('path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'parent_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_or_folders');
    }
};

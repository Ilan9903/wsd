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
        Schema::table('links', function (Blueprint $table) {
            $table->boolean('has_receipt')->default(false);
            $table->boolean('has_watermark')->default(false);
            $table->json('recipients_email_addresses')->nullable();
            $table->string('message_subject')->nullable();
            $table->text('message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn([
                'has_receipt',
                'has_watermark',
                'recipients_email_addresses',
                'message_subject',
                'message',
            ]);
        });
    }
};

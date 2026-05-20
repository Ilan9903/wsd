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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('background');
            $table->string('primary_color');
            $table->string('secondary_color');
            $table->string('button_primary_color');
            $table->string('button_secondary_color');
            $table->string('button_text_primary_color');
            $table->string('button_text_secondary_color');
            $table->string('text_primary_color');
            $table->string('text_secondary_color');
            $table->string('logo_light_theme')->nullable();
            $table->string('logo_dark_theme')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        DB::table('themes')->insert([
            'name' => 'default',
            'background' => '#F2FAFF',
            'primary_color' => '#0088CA',
            'secondary_color' => '#F2FAFFCC',
            'button_primary_color' => '#0088CA',
            'button_secondary_color' => '#F2FAFF',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => '#303030',
            'text_secondary_color' => '#0088CA',
            'logo_light_theme' => '',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};

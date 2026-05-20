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
        Schema::create('client_has_tenant', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('tenant_id');
            $table->uuid('global_client_id');

            $table->unique(['tenant_id', 'global_client_id']);

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants');

            $table->foreign('global_client_id')
                ->references('global_id')
                ->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_has_tenant');
    }
};

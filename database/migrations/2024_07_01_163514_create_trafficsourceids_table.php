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
        Schema::create('trafficsourceids', function (Blueprint $table) {
            $table->id('primary_id');
            $table->bigInteger('id')->nullable();
            $table->bigInteger('traffic_source_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamp('primary_created_at')->nullable();
            $table->timestamp('primary_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trafficsourceids');
    }
};

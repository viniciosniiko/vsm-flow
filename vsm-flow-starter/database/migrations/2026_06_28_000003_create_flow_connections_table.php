<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flow_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_block_id')->constrained('flow_blocks')->cascadeOnDelete();
            $table->foreignId('to_block_id')->constrained('flow_blocks')->cascadeOnDelete();
            $table->string('condition_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_connections');
    }
};

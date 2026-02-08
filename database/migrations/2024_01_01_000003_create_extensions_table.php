<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pbx_connection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('extension_number');
            $table->string('password');
            $table->string('display_name')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_registered')->default(false);
            $table->timestamps();
            $table->unique(['pbx_connection_id', 'extension_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extensions');
    }
};

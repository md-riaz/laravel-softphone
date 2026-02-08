<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('total_calls')->default(0);
            $table->integer('inbound_calls')->default(0);
            $table->integer('outbound_calls')->default(0);
            $table->integer('answered_calls')->default(0);
            $table->integer('missed_calls')->default(0);
            $table->integer('total_duration')->default(0);
            $table->integer('total_talk_time')->default(0);
            $table->decimal('avg_duration', 8, 2)->default(0);
            $table->decimal('avg_talk_time', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['company_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_analytics');
    }
};

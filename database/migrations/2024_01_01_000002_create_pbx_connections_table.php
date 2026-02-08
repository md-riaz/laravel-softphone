<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pbx_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('host');
            $table->integer('port')->default(5060);
            $table->string('wss_url')->comment('WebSocket Secure URL for SIP.js');
            $table->string('stun_server')->default('stun:stun.l.google.com:19302');
            $table->string('turn_server')->nullable();
            $table->string('turn_username')->nullable();
            $table->string('turn_password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pbx_connections');
    }
};

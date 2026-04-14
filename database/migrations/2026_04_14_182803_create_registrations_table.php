<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->uuid('batch_id')->nullable()->index();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->uuid('token')->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index(['slot_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};

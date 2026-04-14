<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->date('date_start');
            $table->date('date_end');
            $table->time('daily_window_start')->default('08:00:00');
            $table->time('daily_window_end')->default('20:00:00');
            $table->string('status', 32)->default('draft');
            $table->string('public_link_token', 64)->unique();
            $table->boolean('notify_organizer_on_registration')->default(false);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

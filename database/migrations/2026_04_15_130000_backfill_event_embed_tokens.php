<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Event::query()->whereNull('embed_token')->each(function (Event $e): void {
            do {
                $token = Str::random(48);
            } while (Event::query()->where('embed_token', $token)->exists());
            $e->update(['embed_token' => $token]);
        });
    }

    public function down(): void
    {
        //
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantEmailToken extends Model
{
    protected $fillable = [
        'email',
        'token',
    ];

    public static function forEmail(string $email): self
    {
        $email = strtolower(trim($email));

        $row = self::query()->where('email', $email)->first();
        if ($row) {
            return $row;
        }

        return self::query()->create([
            'email' => $email,
            'token' => bin2hex(random_bytes(32)),
        ]);
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Generation centralisee des mots de passe temporaires.
 *
 * Utilise Str::password() qui est cryptographiquement sur
 * (random_bytes) et inclut lettres + chiffres + symboles.
 */
class GeneratedPassword
{
    public const DEFAULT_LENGTH = 12;

    public static function make(int $length = self::DEFAULT_LENGTH): string
    {
        return Str::password($length);
    }
}

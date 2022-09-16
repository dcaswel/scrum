<?php

namespace App\Enums;

enum Points: string
{
    case Half = '0.5';
    case One = '1';
    case Two = '2';
    case Three = '3';
    case Five = '5';
    case Eight = '8';
    case Thirteen = '13';
    case TwentyOne = '21';
    case Question = '?';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

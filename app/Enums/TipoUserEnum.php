<?php

namespace App\Enums;

enum TipoUserEnum: string
{
    case Cliente = 'Cliente';
    case Vendedor = 'Vendedor';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}

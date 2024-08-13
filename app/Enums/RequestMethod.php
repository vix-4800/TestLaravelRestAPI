<?php

declare(strict_types=1);

namespace App\Enums;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';

    /**
     * Returns an array of values for all cases in the enum.
     *
     * @return string[] An array of string values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

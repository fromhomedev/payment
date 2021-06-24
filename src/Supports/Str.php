<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Supports;

final class Str
{
    /**
     * @var string[]
     */
    private static array $camelCache = [];

    /**
     * @var array<array-key, string>
     */
    private static array $snakeCache = [];

    public static function camelCase(string $value): string
    {
        if (isset(self::$camelCache[$value])) {
            return self::$camelCache[$value];
        }

        $camelValue = \str_replace('_', '', \lcfirst(\ucwords($value, '_')));

        return self::$camelCache[$value] = $camelValue;
    }

    public static function snake(string $value): string
    {
        if (isset(self::$snakeCache[$value])) {
            return self::$snakeCache[$value];
        }

        if (! \ctype_lower($value)) {
            $value = \preg_replace('/\s+/u', '', ucwords($value));

            $value = self::lower(\preg_replace('/(.)(?=[A-Z])/u', '$1' . '_', $value));
        }

        return self::$snakeCache[$value] = $value;
    }

    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}

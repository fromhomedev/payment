<?php

declare(strict_types=1);

namespace FromHome\Payment\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 */
final class CStore extends Enum
{
    private const ALFAMART = 'ALFAMART';

    private const INDOMART = 'INDOMART';

    public static function ALFAMART(): string
    {
        /** @var string */
        return (new self(self::ALFAMART))->getValue();
    }

    public static function INDOMART(): string
    {
        /** @var string */
        return (new self(self::INDOMART))->getValue();
    }

    public static function midtransCode(string $provider): string
    {
        return match ($provider) {
            self::ALFAMART => 'alfamart',
            self::INDOMART => 'indomart',
            default => throw new \LogicException('Provider not supported : ' . $provider),
        };
    }
}

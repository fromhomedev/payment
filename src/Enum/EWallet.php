<?php

declare(strict_types=1);

namespace FromHome\Payment\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 */
final class EWallet extends Enum
{
    private const QRIS = 'QRIS';

    private const OVO = 'OVO';

    private const GOPAY = 'GOPAY';

    private const DANA = 'DANA';

    private const LINKAJA = 'LINKAJA';

    private const SHOPEEPAY = 'SHOPEEPAY';

    public static function QRIS(): string
    {
        /** @var string */
        return (new self(self::QRIS))->getValue();
    }

    public static function GOPAY(): string
    {
        /** @var string */
        return (new self(self::GOPAY))->getValue();
    }

    public static function SHOPEEPAY(): string
    {
        /** @var string */
        return (new self(self::SHOPEEPAY))->getValue();
    }

    public static function OVO(): string
    {
        /** @var string */
        return (new self(self::OVO))->getValue();
    }

    public static function DANA(): string
    {
        /** @var string */
        return (new self(self::DANA))->getValue();
    }

    public static function LINKAJA(): string
    {
        /** @var string */
        return (new self(self::LINKAJA))->getValue();
    }

    public static function midtransCode(string $provider): string
    {
        return match ($provider) {
            self::QRIS => 'qris',
            self::GOPAY => 'gopay',
            self::SHOPEEPAY => 'shopeepay',
            default => throw new \LogicException('Provider not supported : ' . $provider),
        };
    }

    public static function xenditCode(string $provider): string
    {
        return match ($provider) {
            self::OVO => 'ID_OVO',
            self::DANA => 'ID_DANA',
            self::LINKAJA => 'ID_LINKAJA',
            self::SHOPEEPAY => 'ID_SHOPEEPAY',
            default => throw new \LogicException('Provider not supported : ' . $provider),
        };
    }
}

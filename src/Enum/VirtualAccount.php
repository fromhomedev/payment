<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 */
final class VirtualAccount extends Enum
{
    private const BCA = 'BCA';

    private const BNI = 'BNI';

    private const PERMATA = 'PERMATA';

    private const BRI = 'BRI';

    private const MANDIRI = 'MANDIRI';

    private const BNI_SYARIAH = 'BNI_SYARIAH';

    public static function BCA(): string
    {
        /** @var string */
        return (new self(self::BCA))->getValue();
    }

    public static function BNI(): string
    {
        /** @var string */
        return (new self(self::BNI))->getValue();
    }

    public static function PERMATA(): string
    {
        /** @var string */
        return (new self(self::PERMATA))->getValue();
    }

    public static function BRI(): string
    {
        /** @var string */
        return (new self(self::BRI))->getValue();
    }

    public static function MANDIRI(): string
    {
        /** @var string */
        return (new self(self::MANDIRI))->getValue();
    }

    public static function BNI_SYARIAH(): string
    {
        /** @var string */
        return (new self(self::BNI_SYARIAH))->getValue();
    }

    public static function midtransCode(string $provider): string
    {
        return match ($provider) {
            self::BCA => 'bca',
            self::BNI => 'bni',
            self::PERMATA => 'permata',
            self::BRI => 'bri',
            self::MANDIRI => 'echannel',
            default => throw new \LogicException('Provider not supported : ' . $provider),
        };
    }

    public static function xenditCode(string $provider): string
    {
        return match ($provider) {
            self::BCA => self::BCA,
            self::BNI => self::BNI,
            self::BRI => self::BRI,
            self::PERMATA => self::PERMATA,
            self::MANDIRI => self::MANDIRI,
            self::BNI_SYARIAH => self::BNI_SYARIAH,
            default => throw new \LogicException('Provider not supported : ' . $provider),
        };
    }
}

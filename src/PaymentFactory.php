<?php

declare(strict_types=1);

namespace Ziswapp\Payment;

use Ziswapp\Payment\Contracts\PaymentInterface;
use Ziswapp\Payment\Providers\Xendit\XenditClient;
use Ziswapp\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ziswapp\Payment\Contracts\OutputFactoryInterface;
use Ziswapp\Payment\Providers\Midtrans\MidtransClient;
use Ziswapp\Payment\Providers\Midtrans\SnapMidtransClient;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;

final class PaymentFactory
{
    public const SNAP = 'SNAP';

    public const MIDTRANS = 'MIDTRANS';

    public const XENDIT = 'XENDIT';

    public static function make(
        string $provider,
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ): PaymentInterface {
        return match ($provider) {
            self::SNAP => self::createSnapMidtransClient($credentials, $configurations, $inputFactory, $outputFactory, $httpClient),
            self::MIDTRANS => self::createMidtransClient($credentials, $configurations, $inputFactory, $outputFactory, $httpClient),
            self::XENDIT => self::createXenditClient($credentials, $configurations, $inputFactory, $outputFactory, $httpClient),
            default => throw new \RuntimeException('Provider not supported : ' . $provider)
        };
    }

    public static function makeDefault(string $provider, CredentialsInterface $credentials, array $configurations): PaymentInterface
    {
        return self::make($provider, $credentials, $configurations);
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function createSnapMidtransClient(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ): SnapMidtransClient {
        return new SnapMidtransClient(
            $credentials,
            $configurations,
            $inputFactory,
            $outputFactory,
            $httpClient
        );
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function createMidtransClient(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ): MidtransClient {
        return new MidtransClient(
            $credentials,
            $configurations,
            $inputFactory,
            $outputFactory,
            $httpClient
        );
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function createXenditClient(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ): XenditClient {
        return new XenditClient(
            $credentials,
            $configurations,
            $inputFactory,
            $outputFactory,
            $httpClient
        );
    }
}

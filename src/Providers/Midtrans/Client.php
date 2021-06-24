<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Midtrans;

use Psl\Type;
use Symfony\Component\HttpClient\HttpClient;
use Ziswapp\Payment\Contracts\PaymentInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Ziswapp\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ziswapp\Payment\Contracts\OutputFactoryInterface;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

abstract class Client implements PaymentInterface
{
    public const SANDBOX_URL = 'https://api.sandbox.midtrans.com';

    public const PRODUCTION_URL = 'https://api.midtrans.com';

    protected bool $isProduction;

    protected HttpClientInterface $httpClient;

    protected CredentialsInterface $credentials;

    protected OutputFactoryInterface $outputFactory;

    protected PaymentInputFactoryInterface $inputFactory;

    protected array $configurations;

    /**
     * @param array{ $credentials
     *  isProduction?: bool,
     *  appendNotification?: string,
     *  overrideNotification?: string
     * } $configurations
     */
    public function __construct(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->credentials = $credentials;
        $this->setConfigurations($configurations);

        $isProduction = $this->isProduction = $this->configurations['isProduction'] ?? true;
        $appendNotification = $this->configurations['appendNotification'] ?? '';
        $overrideNotification = $this->configurations['overrideNotification'] ?? '';

        $baseUri = $isProduction ? self::PRODUCTION_URL : self::SANDBOX_URL;

        $this->httpClient = $httpClient ?: HttpClient::createForBaseUri($baseUri, [
            'auth_basic' => $credentials->getSecret(),
            'headers' => [
                'X-Append-Notification' => $appendNotification,
                'X-Override-Notification' => $overrideNotification,
            ],
        ]);
        $this->outputFactory = $outputFactory ?? MidtransOutputFactory::create();
        $this->inputFactory = $inputFactory ?? MidtransInputFactory::create();
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @return array{
     *  isProduction?: bool,
     *  appendNotification?: string,
     *  overrideNotification?: string
     * }
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }

    /**
     * @param array{
     *  isProduction?: bool,
     *  appendNotification?: string,
     *  overrideNotification?: string
     * } $configurations
     */
    public function setConfigurations(array $configurations): self
    {
        $this->configurations = Type\shape([
            'isProduction' => Type\bool(),
            'appendNotification' => Type\optional(Type\non_empty_string()),
            'overrideNotification' => Type\optional(Type\non_empty_string()),
        ])->coerce($configurations);

        return $this;
    }

    public function getCredentials(): CredentialsInterface
    {
        return $this->credentials;
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function executeRequest(string $method, string $uri, array $query = [], array $params = []): ResponseInterface
    {
        return $this->httpClient->request($method, $uri, [
            'query' => $query,
            'json' => $params,
            'headers' => [
                'X-Append-Notification' => $this->configurations['appendNotification'] ?? '',
                'X-Override-Notification' => $this->configurations['overrideNotification'] ?? '',
                'Accept' => 'application/json',
            ],
        ]);
    }
}

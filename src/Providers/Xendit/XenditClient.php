<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Xendit;

use BadMethodCallException;
use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Output\EWalletOutput;
use Symfony\Component\HttpClient\HttpClient;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Contracts\PaymentInterface;
use Ziswapp\Payment\Output\VirtualAccountOutput;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Ziswapp\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ziswapp\Payment\Contracts\OutputFactoryInterface;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class XenditClient implements PaymentInterface
{
    private array $configurations;

    private HttpClientInterface $httpClient;

    private OutputFactoryInterface $outputFactory;

    private PaymentInputFactoryInterface $inputFactory;

    /**
     * @param array{ $credentials
     *  forUserId?: string,
     *  withFeeRule?: string
     * } $configurations
     */
    public function __construct(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory,
        ?OutputFactoryInterface $outputFactory,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->setConfigurations($configurations);

        $this->httpClient = $httpClient ?: HttpClient::createForBaseUri('https://api.xendit.co', [
            'auth_basic' => [$credentials->getSecret(), ''],
        ]);

        $this->outputFactory = $outputFactory ?? XenditOutputFactory::create();
        $this->inputFactory = $inputFactory ?? XenditInputFactory::create();
    }

    /**
     * @param array{
     *  forUserId?: string,
     *  withFeeRule?: string
     * } $configurations
     */
    public function setConfigurations(array $configurations): void
    {
        $this->configurations = $configurations;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createVirtualAccount(VirtualAccountInput $input): VirtualAccountOutput
    {
        $input = $this->inputFactory->fromVirtualAccountInput($input);

        $response = $this->executeRequest('POST', '/callback_virtual_accounts', [], $input->requestBody());

        $data = $response->toArray();

        return $this->outputFactory->fromVirtualAccountArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createEWallet(EWalletInput $input): EWalletOutput
    {
        $input = $this->inputFactory->fromEWalletInput($input);

        $response = $this->executeRequest('POST', '/ewallets/charges', [], $input->requestBody());

        $data = $response->toArray();

        return $this->outputFactory->fromEWalletArray($data);
    }

    public function createConvenienceStore(CStoreInput $input): void
    {
        throw new BadMethodCallException('Not implemented yet');
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function executeRequest(string $method, string $uri, array $query = [], array $params = []): ResponseInterface
    {
        $headers = [
            'xendit-lib' => 'php@unofficial',
        ];

        if (array_key_exists('forUserId', $this->configurations)) {
            $headers['for-user-id'] = $this->configurations['forUserId'];
        }

        if (array_key_exists('withFeeRule', $this->configurations)) {
            $headers['with-fee-rule'] = $this->configurations['withFeeRule'];
        }

        return $this->httpClient->request($method, $uri, [
            'query' => $query,
            'json' => $params,
            'headers' => $headers,
        ]);
    }
}

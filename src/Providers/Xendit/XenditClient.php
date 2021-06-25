<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Xendit;

use Ziswapp\Payment\Enum\CStore;
use Ziswapp\Payment\Enum\EWallet;
use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Enum\VirtualAccount;
use Ziswapp\Payment\Output\CStoreOutput;
use Ziswapp\Payment\Output\EWalletOutput;
use Ziswapp\Payment\Input\CheckStatusInput;
use Symfony\Component\HttpClient\HttpClient;
use Ziswapp\Payment\Input\CancelPaymentInput;
use Ziswapp\Payment\Output\CheckStatusOutput;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Contracts\PaymentInterface;
use Ziswapp\Payment\Output\VirtualAccountOutput;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Ziswapp\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ziswapp\Payment\Contracts\OutputFactoryInterface;
use Ziswapp\Payment\Contracts\PaymentOperationInterface;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class XenditClient implements PaymentInterface, PaymentOperationInterface
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
    )
    {
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

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createConvenienceStore(CStoreInput $input): CStoreOutput
    {
        $input = $this->inputFactory->fromCStoreInput($input);

        $response = $this->executeRequest('POST', '/fixed_payment_code', [], $input->requestBody());

        $data = $response->toArray();

        return $this->outputFactory->fromCStoreArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function status(CheckStatusInput $input): CheckStatusOutput
    {
        return match ($input->getProviderCode()) {
            EWallet::SHOPEEPAY(), EWallet::OVO(), EWallet::DANA(), EWallet::LINKAJA() => $this->statusEWallet($input),
            VirtualAccount::BNI(), VirtualAccount::BNI_SYARIAH(), VirtualAccount::BCA(),
            VirtualAccount::BRI(), VirtualAccount::MANDIRI(), VirtualAccount::PERMATA() => $this->statusVirtualAccount($input),
            CStore::ALFAMART(), CStore::INDOMART() => $this->statusConvenienceStore($input),
            default => throw new \RuntimeException('Provider code not supported'),
        };
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function cancel(CancelPaymentInput $input): CheckStatusOutput
    {
        return match ($input->getProviderCode()) {
            EWallet::SHOPEEPAY(), EWallet::OVO(), EWallet::DANA(), EWallet::LINKAJA() => throw new \RuntimeException('Cannot cancel eWallet transaction'),
            CStore::ALFAMART(), CStore::INDOMART() => $this->cancelConvenienceStore($input),
            VirtualAccount::BNI(), VirtualAccount::BNI_SYARIAH(), VirtualAccount::BCA(),
            VirtualAccount::BRI(), VirtualAccount::MANDIRI(), VirtualAccount::PERMATA() => $this->cancelVirtualAccount($input),
            default => throw new \RuntimeException('Provider code not supported'),
        };
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

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function statusEWallet(CheckStatusInput $input): CheckStatusOutput
    {
        $response = $this->executeRequest('GET', '/ewallets/charges/' . $input->getTransaction()->getId());

        $data = $response->toArray();

        return $this->outputFactory->fromStatusArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function statusVirtualAccount(CheckStatusInput $input): CheckStatusOutput
    {
        $response = $this->executeRequest('GET', '/callback_virtual_accounts/' . $input->getTransaction()->getId());

        $data = $response->toArray();

        return $this->outputFactory->fromStatusArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function statusConvenienceStore(CheckStatusInput $input): CheckStatusOutput
    {
        $response = $this->executeRequest('GET', '/fixed_payment_code/' . $input->getTransaction()->getId());

        $data = $response->toArray();

        return $this->outputFactory->fromStatusArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function cancelVirtualAccount(CancelPaymentInput $input): CheckStatusOutput
    {
        $response = $this->executeRequest('PATCH', '/callback_virtual_accounts/' . $input->getTransaction()->getId(), [], [
            'expiration_date' => '1970-01-01T00:00:00Z',
        ]);

        $data = $response->toArray();

        return $this->outputFactory->fromStatusArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function cancelConvenienceStore(CancelPaymentInput $input): CheckStatusOutput
    {
        $response = $this->executeRequest('PATCH', '/fixed_payment_code/' . $input->getTransaction()->getId(), [], [
            'expiration_date' => '1970-01-01T00:00:00Z',
        ]);

        $data = $response->toArray();

        return $this->outputFactory->fromStatusArray($data);
    }
}

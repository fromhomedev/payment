<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use FromHome\Payment\Output\CStoreOutput;
use FromHome\Payment\Output\EWalletOutput;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Exceptions\PaymentException;
use FromHome\Payment\Output\VirtualAccountOutput;
use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use Symfony\Contracts\HttpClient\ResponseInterface;
use FromHome\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use FromHome\Payment\Contracts\OutputFactoryInterface;
use FromHome\Payment\Input\CheckStatusTransactionInput;
use FromHome\Payment\Input\CancelPaymentTransactionInput;
use FromHome\Payment\Input\VirtualAccountTransactionInput;
use FromHome\Payment\Contracts\PaymentInputFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class SnapMidtransClient extends Client
{
    public const SANDBOX_URL = 'https://app.sandbox.midtrans.com';

    public const PRODUCTION_URL = 'https://app.midtrans.com';

    public function __construct(
        CredentialsInterface $credentials,
        array $configurations,
        ?PaymentInputFactoryInterface $inputFactory = null,
        ?OutputFactoryInterface $outputFactory = null,
        ?HttpClientInterface $httpClient = null
    ) {
        parent::__construct(
            $credentials,
            $configurations,
            $inputFactory ?? SnapMidtransInputFactory::create(),
            $outputFactory ?? SnapMidtransOutputFactory::create(),
            $httpClient
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function createVirtualAccount(VirtualAccountTransactionInput $input): VirtualAccountOutput
    {
        $input = SnapMidtransInputFactory::create()->fromVirtualAccountInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        $data = $response->toArray();

        return $this->outputFactory->fromVirtualAccountArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function createEWallet(EWalletTransactionInput $input): EWalletOutput
    {
        $input = SnapMidtransInputFactory::create()->fromEWalletInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        $data = $response->toArray();

        return $this->outputFactory->fromEWalletArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function createConvenienceStore(CStoreTransactionInput $input): CStoreOutput
    {
        $input = SnapMidtransInputFactory::create()->fromCStoreInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

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
    public function status(CheckStatusTransactionInput $input): CheckStatusOutput
    {
        $client = $this->makeMidtransClient();

        return $client->status($input);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function cancel(CancelPaymentTransactionInput $input): CheckStatusOutput
    {
        $client = $this->makeMidtransClient();

        return $client->cancel($input);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function createTransaction(array $body): ResponseInterface
    {
        return $this->executeRequest('POST', '/snap/v1/transactions', [], $body);
    }

    protected function makeMidtransClient(): MidtransClient
    {
        return new MidtransClient(
            $this->credentials,
            $this->configurations,
            $this->inputFactory,
            $this->outputFactory,
            $this->httpClient
        );
    }
}

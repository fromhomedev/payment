<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use InvalidArgumentException;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Input\RedirectPaymentInput;
use FromHome\Payment\Exceptions\PaymentException;
use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use Symfony\Contracts\HttpClient\ResponseInterface;
use FromHome\Payment\Contracts\CredentialsInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use FromHome\Payment\Contracts\OutputFactoryInterface;
use FromHome\Payment\Output\RedirectTransactionOutput;
use FromHome\Payment\Input\CheckStatusTransactionInput;
use FromHome\Payment\Contracts\RedirectPaymentInterface;
use FromHome\Payment\Input\CancelPaymentTransactionInput;
use FromHome\Payment\Input\VirtualAccountTransactionInput;
use FromHome\Payment\Contracts\PaymentInputFactoryInterface;
use FromHome\Payment\Contracts\RedirectInputFactoryInterface;
use FromHome\Payment\Contracts\RedirectOutputFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class SnapMidtransClient extends Client implements RedirectPaymentInterface
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
    public function createVirtualAccount(VirtualAccountTransactionInput $input): RedirectTransactionOutput
    {
        $input = $this->inputFactory->fromVirtualAccountInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        $data = $response->toArray();

        /** @psalm-var RedirectTransactionOutput */
        return $this->outputFactory->fromVirtualAccountArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function createEWallet(EWalletTransactionInput $input): RedirectTransactionOutput
    {
        $input = $this->inputFactory->fromEWalletInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        $data = $response->toArray();

        /** @psalm-var RedirectTransactionOutput */
        return $this->outputFactory->fromEWalletArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function createConvenienceStore(CStoreTransactionInput $input): RedirectTransactionOutput
    {
        $input = $this->inputFactory->fromCStoreInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        $data = $response->toArray();

        /** @psalm-var RedirectTransactionOutput */
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createUrl(RedirectPaymentInput $input): RedirectTransactionOutput
    {
        if (! $this->inputFactory instanceof RedirectInputFactoryInterface) {
            throw new InvalidArgumentException(\sprintf('Input factory must be instance of `%s`', RedirectInputFactoryInterface::class));
        }

        $input = $this->inputFactory->fromRedirectInput($input);

        $response = $this->createTransaction($input->requestBody());

        if ($response->getStatusCode() !== 201) {
            throw new PaymentException($response, $response->getStatusCode(), $response->getContent(false));
        }

        if (! $this->outputFactory instanceof RedirectOutputFactoryInterface) {
            throw new InvalidArgumentException(\sprintf('Output factory must be instance of `%s`', RedirectOutputFactoryInterface::class));
        }

        $data = $response->toArray();

        /** @psalm-var RedirectTransactionOutput */
        return $this->outputFactory->fromRedirectArray($data);
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

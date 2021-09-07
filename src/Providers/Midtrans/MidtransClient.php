<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use InvalidArgumentException;
use FromHome\Payment\Output\CStoreOutput;
use FromHome\Payment\Output\EWalletOutput;
use FromHome\Payment\Input\ChargeCardInput;
use FromHome\Payment\Output\ChargeCardOutput;
use FromHome\Payment\Input\CardBinFilterInput;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Output\CardBinFilterOutput;
use FromHome\Payment\Exceptions\PaymentException;
use FromHome\Payment\Output\VirtualAccountOutput;
use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use FromHome\Payment\Contracts\CardPaymentInterface;
use FromHome\Payment\Contracts\UtilOperationInterface;
use FromHome\Payment\Input\CheckStatusTransactionInput;
use FromHome\Payment\Contracts\CardInputFactoryInterface;
use FromHome\Payment\Input\CancelPaymentTransactionInput;
use FromHome\Payment\Contracts\CardOutputFactoryInterface;
use FromHome\Payment\Input\VirtualAccountTransactionInput;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class MidtransClient extends Client implements UtilOperationInterface, CardPaymentInterface
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createVirtualAccount(VirtualAccountTransactionInput $input): VirtualAccountOutput
    {
        $input = $this->inputFactory->fromVirtualAccountInput($input);

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        /** @psalm-var VirtualAccountOutput */
        return $this->outputFactory->fromVirtualAccountArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createEWallet(EWalletTransactionInput $input): EWalletOutput
    {
        $input = $this->inputFactory->fromEWalletInput($input);

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        /** @psalm-var EWalletOutput */
        return $this->outputFactory->fromEWalletArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createConvenienceStore(CStoreTransactionInput $input): CStoreOutput
    {
        $input = $this->inputFactory->fromCStoreInput($input);

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        /** @psalm-var CStoreOutput */
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
        $response = $this->executeRequest('POST', \sprintf('/v2/%s/status', $input->getTransaction()->getId()));

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        /** @psalm-var CheckStatusOutput */
        return $this->outputFactory->fromStatusArray($data);
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
        $response = $this->executeRequest('POST', \sprintf('/v2/%s/cancel', $input->getTransaction()->getId()));

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        /** @psalm-var CheckStatusOutput */
        return $this->outputFactory->fromStatusArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function binInfo(CardBinFilterInput $input): CardBinFilterOutput
    {
        $response = $this->executeRequest('GET', \sprintf('/v1/bins/%s', $input->getNumber()));

        $data = $response->toArray();

        /** @psalm-var CardBinFilterOutput */
        return $this->outputFactory->fromFilterBinArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function charge(ChargeCardInput $input): ChargeCardOutput
    {
        if (! $this->inputFactory instanceof CardInputFactoryInterface) {
            throw new InvalidArgumentException(\sprintf('Input factory must be instance of `%s`', CardInputFactoryInterface::class));
        }

        $input = $this->inputFactory->fromChargeInput($input);

        $response = $this->executeRequest('POST', '/v2/charge', $input->requestBody());

        $data = $response->toArray();

        if ((int) $data['status_code'] !== 200 && (int) $data['status_code'] !== 201) {
            throw new PaymentException($response, (int) $data['status_code'], $data['status_message']);
        }

        if (! $this->outputFactory instanceof CardOutputFactoryInterface) {
            throw new InvalidArgumentException(\sprintf('Output factory must be instance of `%s`', CardOutputFactoryInterface::class));
        }

        return $this->outputFactory->fromChargeArray($data);
    }
}

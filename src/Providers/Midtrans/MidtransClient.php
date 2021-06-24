<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Midtrans;

use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Output\CStoreOutput;
use Ziswapp\Payment\Output\EWalletOutput;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Output\VirtualAccountOutput;
use Ziswapp\Payment\Exceptions\PaymentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class MidtransClient extends Client
{
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

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int)$data['status_code'] !== 200 && (int)$data['status_code'] !== 201) {
            throw new PaymentException($response, (int)$data['status_code'], $data['status_message']);
        }

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

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int)$data['status_code'] !== 200 && (int)$data['status_code'] !== 201) {
            throw new PaymentException($response, (int)$data['status_code'], $data['status_message']);
        }

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

        $response = $this->executeRequest('POST', '/v2/charge', [], $input->requestBody());

        $data = $response->toArray();

        if ((int)$data['status_code'] !== 200 && (int)$data['status_code'] !== 201) {
            throw new PaymentException($response, (int)$data['status_code'], $data['status_message']);
        }

        return $this->outputFactory->fromCStoreArray($data);
    }
}

<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use Psl\Type;
use FromHome\Payment\Output\CStoreOutput;
use FromHome\Payment\Output\EWalletOutput;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Output\CardBinFilterOutput;
use FromHome\Payment\Output\VirtualAccountOutput;
use FromHome\Payment\Contracts\OutputFactoryInterface;
use FromHome\Payment\Exceptions\MethodNotImplementedException;

final class SnapMidtransOutputFactory implements OutputFactoryInterface
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromStatusArray(array $data): CheckStatusOutput
    {
        return MidtransOutputFactory::create()->fromStatusArray($data);
    }

    public function fromVirtualAccountArray(array $data): VirtualAccountOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return VirtualAccountOutput::create(
            $data['redirect_url'],
            'snap',
            null,
            null,
            null,
            $data
        );
    }

    public function fromCStoreArray(array $data): CStoreOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return CStoreOutput::create(
            $data['redirect_url'],
            'snap',
            null,
            null,
            null,
            $data
        );
    }

    public function fromEWalletArray(array $data): EWalletOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return EWalletOutput::create(
            'snap',
            null,
            null,
            null,
            $data['redirect_url'],
            null,
            $data['redirect_url'],
            $data['redirect_url'],
            $data
        );
    }

    /**
     * @throws MethodNotImplementedException
     */
    public function fromFilterBinArray(array $data): CardBinFilterOutput
    {
        throw new MethodNotImplementedException(sprintf('This method `%s` not implemented', __FUNCTION__));
    }
}

<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use Psl\Type;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Output\CardBinFilterOutput;
use FromHome\Payment\Contracts\OutputFactoryInterface;
use FromHome\Payment\Output\RedirectTransactionOutput;
use FromHome\Payment\Contracts\RedirectOutputFactoryInterface;
use FromHome\Payment\Exceptions\MethodNotImplementedException;

final class SnapMidtransOutputFactory implements OutputFactoryInterface, RedirectOutputFactoryInterface
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

    public function fromVirtualAccountArray(array $data): RedirectTransactionOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return RedirectTransactionOutput::create(
            $data['token'],
            $data['redirect_url'],
            $data
        );
    }

    public function fromCStoreArray(array $data): RedirectTransactionOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return RedirectTransactionOutput::create(
            $data['token'],
            $data['redirect_url'],
            $data
        );
    }

    public function fromEWalletArray(array $data): RedirectTransactionOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return RedirectTransactionOutput::create(
            $data['token'],
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

    public function fromRedirectArray(array $data): RedirectTransactionOutput
    {
        $data = Type\shape([
            'token' => Type\non_empty_string(),
            'redirect_url' => Type\non_empty_string(),
        ])->coerce($data);

        return RedirectTransactionOutput::create(
            $data['token'],
            $data['redirect_url'],
            $data
        );
    }
}

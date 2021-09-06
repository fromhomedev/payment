<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use FromHome\Payment\Input\VirtualAccountTransactionInput;

interface PaymentInputFactoryInterface
{
    public function fromCStoreInput(CStoreTransactionInput $input): InputInterface;

    public function fromVirtualAccountInput(VirtualAccountTransactionInput $input): InputInterface;

    public function fromEWalletInput(EWalletTransactionInput $input): InputInterface;
}

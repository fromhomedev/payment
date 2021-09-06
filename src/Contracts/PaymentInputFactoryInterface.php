<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CStoreTransactionInput;
use Ziswapp\Payment\Input\EWalletTransactionInput;
use Ziswapp\Payment\Input\VirtualAccountTransactionInput;

interface PaymentInputFactoryInterface
{
    public function fromCStoreInput(CStoreTransactionInput $input): InputInterface;

    public function fromVirtualAccountInput(VirtualAccountTransactionInput $input): InputInterface;

    public function fromEWalletInput(EWalletTransactionInput $input): InputInterface;
}

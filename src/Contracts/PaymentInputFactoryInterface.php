<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Input\VirtualAccountInput;

interface PaymentInputFactoryInterface
{
    public function fromCStoreInput(CStoreInput $input): InputInterface;

    public function fromVirtualAccountInput(VirtualAccountInput $input): InputInterface;

    public function fromEWalletInput(EWalletInput $input): InputInterface;
}

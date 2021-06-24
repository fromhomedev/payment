<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Output\CStoreOutput;
use Ziswapp\Payment\Output\EWalletOutput;
use Ziswapp\Payment\Output\VirtualAccountOutput;

interface OutputFactoryInterface
{
    public function fromVirtualAccountArray(array $data): VirtualAccountOutput;

    public function fromCStoreArray(array $data): CStoreOutput;

    public function fromEWalletArray(array $data): EWalletOutput;
}

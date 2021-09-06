<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Output\CStoreOutput;
use FromHome\Payment\Output\EWalletOutput;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Output\CardBinFilterOutput;
use FromHome\Payment\Output\VirtualAccountOutput;

interface OutputFactoryInterface
{
    public function fromStatusArray(array $data): CheckStatusOutput;

    public function fromVirtualAccountArray(array $data): VirtualAccountOutput;

    public function fromCStoreArray(array $data): CStoreOutput;

    public function fromEWalletArray(array $data): EWalletOutput;

    public function fromFilterBinArray(array $data): CardBinFilterOutput;
}

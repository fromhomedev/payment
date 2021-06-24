<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Input\VirtualAccountInput;

interface PaymentInterface
{
    public const VERSION = '1.0';

    /**
     * @return mixed
     */
    public function createVirtualAccount(VirtualAccountInput $input);

    /**
     * @return mixed
     */
    public function createEWallet(EWalletInput $input);

    /**
     * @return mixed
     */
    public function createConvenienceStore(CStoreInput $input);
}

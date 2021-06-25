<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CheckStatusInput;
use Ziswapp\Payment\Input\CancelPaymentInput;

interface PaymentOperationInterface
{
    /**
     * @return mixed
     */
    public function status(CheckStatusInput $input);

    /**
     * @return mixed
     */
    public function cancel(CancelPaymentInput $input);
}

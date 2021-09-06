<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\ChargeCardInput;

interface CardPaymentInterface
{
    /**
     * @return mixed
     */
    public function charge(ChargeCardInput $input);
}

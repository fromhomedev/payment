<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\ChargeCardInput;

interface CardPaymentInterface
{
    /**
     * @return mixed
     */
    public function charge(ChargeCardInput $input);
}

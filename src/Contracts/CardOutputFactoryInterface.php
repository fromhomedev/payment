<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Output\ChargeCardOutput;

interface CardOutputFactoryInterface
{
    public function fromChargeArray(array $data): ChargeCardOutput;
}

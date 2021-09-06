<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Output\ChargeCardOutput;

interface CardOutputFactoryInterface
{
    public function fromChargeArray(array $data): ChargeCardOutput;
}

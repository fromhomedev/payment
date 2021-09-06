<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\ChargeCardInput;

interface CardInputFactoryInterface extends InputInterface
{
    public function fromChargeInput(ChargeCardInput $input): self;
}

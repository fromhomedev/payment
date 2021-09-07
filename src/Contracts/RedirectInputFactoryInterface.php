<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\RedirectPaymentInput;

interface RedirectInputFactoryInterface extends InputInterface
{
    public function fromRedirectInput(RedirectPaymentInput $input): self;
}

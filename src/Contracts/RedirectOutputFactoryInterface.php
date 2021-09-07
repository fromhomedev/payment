<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

interface RedirectOutputFactoryInterface
{
    /**
     * @return mixed
     */
    public function fromRedirectArray(array $data);
}

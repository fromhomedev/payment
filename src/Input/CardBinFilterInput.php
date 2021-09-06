<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

final class CardBinFilterInput
{
    private string $number;

    public function __construct(string $number)
    {
        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}

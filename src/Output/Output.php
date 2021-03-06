<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

abstract class Output
{
    protected array $originalOutput;

    protected function __construct()
    {
    }

    public function getOriginalOutput(): array
    {
        return $this->originalOutput;
    }
}

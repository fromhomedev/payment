<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

final class CardBinFilterOutput extends Output
{
    private string $number;

    private string $type;

    public static function create(string $number, string $type, array $originalOutput = []): self
    {
        $self = new self();

        $self->originalOutput = $originalOutput;
        $self->number = $number;
        $self->type = $type;

        return $self;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

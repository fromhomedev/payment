<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

final class CheckStatusOutput extends TransactionOutput
{
    public static function create(string $transactionId, string $status, array $originalOutput = []): self
    {
        $self = new self();

        $self->transactionId = $transactionId;
        $self->status = $status;

        $self->originalOutput = $originalOutput;

        return $self;
    }
}

<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Output;

final class VirtualAccountOutput extends TransactionOutput
{
    protected string $paymentNumber;

    public static function create(
        string $paymentNumber,
        string $transactionId,
        ?string $orderId = null,
        ?string $status = null,
        ?float $amount = null,
        array $originalOutput = []
    ): self {
        $self = new self();

        $self->paymentNumber = $paymentNumber;
        $self->transactionId = $transactionId;
        $self->orderId = $orderId;
        $self->status = $status;
        $self->amount = $amount;
        $self->originalOutput = $originalOutput;

        return $self;
    }

    public function getPaymentNumber(): string
    {
        return $this->paymentNumber;
    }
}

<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Output;

final class EWalletOutput extends TransactionOutput
{
    protected ?string $webUrl = null;

    protected ?string $mobileUrl = null;

    protected ?string $deeplinkUrl = null;

    protected ?string $qrCode = null;

    public static function create(
        string $transactionId,
        ?string $orderId = null,
        ?string $status = null,
        ?float $amount = null,
        ?string $deeplinkUrl = null,
        ?string $qrCode = null,
        ?string $webUrl = null,
        ?string $mobileUrl = null,
        array $originalOutput = []
    ): self {
        $self = new self();

        $self->transactionId = $transactionId;
        $self->orderId = $orderId;
        $self->status = $status;
        $self->amount = $amount;
        $self->webUrl = $webUrl;
        $self->mobileUrl = $mobileUrl;
        $self->deeplinkUrl = $deeplinkUrl;
        $self->qrCode = $qrCode;
        $self->originalOutput = $originalOutput;

        return $self;
    }

    public function getWebUrl(): ?string
    {
        return $this->webUrl;
    }

    public function getMobileUrl(): ?string
    {
        return $this->mobileUrl;
    }

    public function getDeeplinkUrl(): ?string
    {
        return $this->deeplinkUrl;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }
}

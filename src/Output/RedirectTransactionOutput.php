<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

final class RedirectTransactionOutput extends Output
{
    private string $redirectToken;

    private string $redirectUrl;

    public static function create(string $redirectToken, string $redirectUrl, array $originalOutput): self
    {
        $self = new self();

        $self->redirectToken = $redirectToken;
        $self->redirectUrl = $redirectUrl;
        $self->originalOutput = $originalOutput;

        return $self;
    }

    public function getRedirectToken(): string
    {
        return $this->redirectToken;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}

<?php

declare(strict_types=1);

namespace FromHome\Payment\ValueObject;

use Psl\Type;

final class VirtualAccount
{
    private string $providerCode;

    private ?string $number;

    private array $input = [];

    public function __construct(array $input)
    {
        $input = Type\shape([
            'providerCode' => Type\non_empty_string(),
            'number' => Type\optional(Type\non_empty_string()),
        ], true)->coerce($input);

        $this->providerCode = $input['providerCode'];
        $this->number = $input['number'] ?? null;

        $this->input = $input;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function getInput(): array
    {
        return $this->input;
    }
}

<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use FromHome\Payment\Enum\CStore;
use FromHome\Payment\Enum\VirtualAccount;
use FromHome\Payment\ValueObject\Transaction;
use FromHome\Payment\Contracts\InputInterface;
use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use FromHome\Payment\Input\VirtualAccountTransactionInput;
use FromHome\Payment\Contracts\PaymentInputFactoryInterface;
use FromHome\Payment\Providers\Midtrans\Concerns\InputRequestBody;

final class SnapMidtransInputFactory implements InputInterface, PaymentInputFactoryInterface
{
    use InputRequestBody;

    protected ?array $params = null;

    protected Transaction $transaction;

    protected function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromCStoreInput(CStoreTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = array_merge($this->defaultParams(), [
            'enabled_payments' => [
                CStore::midtransCode(
                    $input->getCStore()->getProviderCode()
                ),
            ],
            'cstore' => [
                'alfamart_free_text_1' => $input->getCStore()->getMessage(),
            ],
        ]);

        return $this;
    }

    public function fromVirtualAccountInput(VirtualAccountTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = array_merge($this->defaultParams(), [
            'enabled_payments' => [
                VirtualAccount::midtransCode(
                    $input->getAccount()->getProviderCode()
                ),
            ],
            'bca_va' => [
                'va_number' => $input->getAccount()->getNumber(),
            ],
            'bni_va' => [
                'va_number' => $input->getAccount()->getNumber(),
            ],
            'bri_va' => [
                'va_number' => $input->getAccount()->getNumber(),
            ],
            'permata_va' => [
                'va_number' => $input->getAccount()->getNumber(),
            ],
        ]);

        return $this;
    }

    public function fromEWalletInput(EWalletTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = array_merge($this->defaultParams(), [
            'enabled_payments' => [
                CStore::midtransCode(
                    $input->getWallet()->getProviderCode()
                ),
            ],
            'shopeepay' => [
                'callback_url' => $input->getWallet()->getSuccessUrl(),
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => $input->getWallet()->getSuccessUrl(),
            ],
        ]);

        return $this;
    }

    public function requestBody(): array
    {
        if ($this->params) {
            return $this->params;
        }

        throw new \LogicException('Params or input must be set.');
    }

    protected function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }
}

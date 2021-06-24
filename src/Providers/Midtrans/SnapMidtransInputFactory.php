<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Midtrans;

use Ziswapp\Payment\Enum\CStore;
use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Enum\VirtualAccount;
use Ziswapp\Payment\ValueObject\Transaction;
use Ziswapp\Payment\Contracts\InputInterface;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;
use Ziswapp\Payment\Providers\Midtrans\Concerns\InputRequestBody;

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

    public function fromCStoreInput(CStoreInput $input): self
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

    public function fromVirtualAccountInput(VirtualAccountInput $input): self
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

    public function fromEWalletInput(EWalletInput $input): self
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

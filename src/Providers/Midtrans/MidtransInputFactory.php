<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Midtrans;

use Ziswapp\Payment\Enum\CStore;
use Ziswapp\Payment\Enum\EWallet;
use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Enum\VirtualAccount;
use Ziswapp\Payment\ValueObject\Transaction;
use Ziswapp\Payment\Contracts\InputInterface;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;
use Ziswapp\Payment\Providers\Midtrans\Concerns\InputRequestBody;

final class MidtransInputFactory implements InputInterface, PaymentInputFactoryInterface
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
            'payment_type' => 'cstore',
            'cstore' => [
                'store' => CStore::midtransCode(
                    $input->getCStore()->getProviderCode()
                ),
                'alfamart_free_text_1' => $input->getCStore()->getMessage(),
            ],
        ]);

        return $this;
    }

    public function fromVirtualAccountInput(VirtualAccountInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = array_merge($this->defaultParams(), [
            'bank_transfer' => [
                'bank' => VirtualAccount::midtransCode(
                    $input->getAccount()->getProviderCode()
                ),
                'va_number' => $input->getAccount()->getNumber(),
            ],
            'payment_type' => 'bank_transfer',
        ]);

        return $this;
    }

    public function fromEWalletInput(EWalletInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = array_merge($this->defaultParams(), [
            'payment_type' => EWallet::midtransCode(
                $input->getWallet()->getProviderCode()
            ),
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => $input->getWallet()->getSuccessUrl(),
            ],
            'shopeepay' => [
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

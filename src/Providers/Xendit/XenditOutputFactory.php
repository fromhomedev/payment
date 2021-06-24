<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Xendit;

use Psl\Type;
use Ziswapp\Payment\Output\CStoreOutput;
use Ziswapp\Payment\Output\EWalletOutput;
use Ziswapp\Payment\Output\VirtualAccountOutput;
use Ziswapp\Payment\Contracts\OutputFactoryInterface;

final class XenditOutputFactory implements OutputFactoryInterface
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromVirtualAccountArray(array $data): VirtualAccountOutput
    {
        $data = Type\shape([
            'id' => Type\non_empty_string(),
            'account_number' => Type\non_empty_string(),
            'external_id' => Type\non_empty_string(),
            'status' => Type\non_empty_string(),
            'suggested_amount' => Type\float(),
        ], true)->coerce($data);

        return VirtualAccountOutput::create(
            $data['account_number'],
            $data['id'],
            $data['external_id'],
            $data['status'],
            $data['suggested_amount'],
            $data
        );
    }

    public function fromCStoreArray(array $data): CStoreOutput
    {
        $data = Type\shape([
            'id' => Type\non_empty_string(),
            'external_id' => Type\non_empty_string(),
            'expected_amount' => Type\float(),
            'status' => Type\non_empty_string(),
            'payment_code' => Type\non_empty_string(),
        ], true)->coerce($data);

        return CStoreOutput::create(
            $data['payment_code'],
            $data['id'],
            $data['external_id'],
            $data['status'],
            $data['expected_amount'],
            $data
        );
    }

    public function fromEWalletArray(array $data): EWalletOutput
    {
        $data = Type\shape([
            'id' => Type\non_empty_string(),
            'reference_id' => Type\non_empty_string(),
            'capture_amount' => Type\float(),
            'status' => Type\non_empty_string(),
            'actions' => Type\shape([
                'desktop_web_checkout_url' => Type\nullable(Type\non_empty_string()),
                'mobile_web_checkout_url' => Type\nullable(Type\non_empty_string()),
                'mobile_deeplink_checkout_url' => Type\nullable(Type\non_empty_string()),
                'qr_checkout_string' => Type\nullable(Type\non_empty_string()),
            ]),
        ], true)->coerce($data);

        return EWalletOutput::create(
            $data['id'],
            $data['reference_id'],
            $data['status'],
            $data['capture_amount'],
            $data['actions']['mobile_deeplink_checkout_url'],
            $data['actions']['qr_checkout_string'],
            $data['actions']['desktop_web_checkout_url'],
            $data['actions']['mobile_web_checkout_url'],
            $data,
        );
    }
}

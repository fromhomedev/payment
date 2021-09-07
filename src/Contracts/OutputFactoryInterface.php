<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

interface OutputFactoryInterface
{
    /**
     * @return mixed
     */
    public function fromStatusArray(array $data);

    /**
     * @return mixed
     */
    public function fromVirtualAccountArray(array $data);

    /**
     * @return mixed
     */
    public function fromCStoreArray(array $data);

    /**
     * @return mixed
     */
    public function fromEWalletArray(array $data);

    /**
     * @return mixed
     */
    public function fromFilterBinArray(array $data);
}

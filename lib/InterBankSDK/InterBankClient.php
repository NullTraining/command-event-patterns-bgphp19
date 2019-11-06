<?php

declare(strict_types=1);

namespace InterBankSDK;

class InterBankClient
{
    public function send(string $transactionId, string $toAccountId, int $amount): void
    {
        $time = microtime(true);

        $data = [
            'transactionId' => $transactionId,
            'toAccountId'   => $toAccountId,
            'amount'        => $amount,
        ];

        $env  = getenv('APP_ENV');
        $path = __DIR__.'/../../data/'.$env.'/interbank/'.$time.'-send.json';
        file_put_contents($path, json_encode($data));
    }

    public function confirm(string $transactionId, string $fromAccountId, int $amount): void
    {
        $time = microtime(true);

        $data = [
            'transactionId' => $transactionId,
            'fromAccountId' => $fromAccountId,
            'amount'        => $amount,
        ];

        $env  = getenv('APP_ENV');
        $path = __DIR__.'/../../data/'.$env.'/interbank/'.$time.'-confirm.json';
        file_put_contents($path, json_encode($data));
    }
}

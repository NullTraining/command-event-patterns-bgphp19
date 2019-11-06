<?php

declare(strict_types=1);

namespace FinancialAuthoritySDK;

class TransactionSender
{
    public function send(string $fromAccountId, string $toAccountId, int $amount): void
    {
        $time = microtime(true);

        $data = [
            'fromAccountId' => $fromAccountId,
            'toAccountId'   => $toAccountId,
            'amount'        => $amount,
        ];

        $env  = getenv('APP_ENV');
        $path = __DIR__.'/../../data/'.$env.'/financial-authority/'.$time.'.json';
        file_put_contents($path, json_encode($data));
    }
}

<?php

declare(strict_types=1);

namespace ShortMessageServiceSDK;

class SmsSender
{
    public function send(string $phoneNumber, string $message): void
    {
        $time = microtime(true);

        $data = [
            'phoneNumber' => $phoneNumber,
            'message'     => $message,
            'time'        => $time,
        ];

        $env  = getenv('APP_ENV');
        $path = __DIR__.'/../../data/'.$env.'/sms/'.$time.'.json';
        file_put_contents($path, json_encode($data));
    }
}

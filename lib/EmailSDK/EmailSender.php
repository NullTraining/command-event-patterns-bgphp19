<?php

declare(strict_types=1);

namespace EmailSDK;

class EmailSender
{
    public function send(string $from, string $to, string $subject, string $body): void
    {
        $time = microtime(true);

        $data = [
            'from'    => $from,
            'to'      => $to,
            'subject' => $subject,
            'body'    => $body,
            'time'    => $time,
        ];

        $env  = getenv('APP_ENV');
        $path = __DIR__.'/../../data/'.$env.'/emails/'.$time.'.json';
        file_put_contents($path, json_encode($data));
    }
}

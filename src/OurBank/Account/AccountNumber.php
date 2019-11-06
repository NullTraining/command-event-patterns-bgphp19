<?php

declare(strict_types=1);

namespace App\OurBank\Account;

class AccountNumber
{
    /** @var string */
    private $accountNumber;

    public function __construct(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
}

<?php

declare(strict_types=1);

namespace App\OurBank\Account;

use App\OurBank\Customer\CustomerId;

class Account
{
    /** @var AccountId */
    private $accountId;

    /** @var CustomerId */
    private $customerId;

    /** @var int */
    private $balance;

    public function __construct(
        AccountId $accountId,
        CustomerId $customerId,
        int $balance
    ) {
        $this->accountId  = $accountId;
        $this->customerId = $customerId;
        $this->balance    = $balance;
    }

    public function canWithdraw(int $amount): bool
    {
        if ($this->balance > $amount) {
            return true;
        }

        return false;
    }

    public function withdraw(int $amount): void
    {
        if (false === $this->canWithdraw($amount)) {
            throw new \Exception('NOT ENOUGH MONEY');
        }
        $this->balance -= $amount;
    }

    public function deposit(int $amount): void
    {
        $this->balance += $amount;
    }

    public function getAccountId(): AccountId
    {
        return $this->accountId;
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

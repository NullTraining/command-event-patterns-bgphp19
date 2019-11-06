<?php

declare(strict_types=1);

namespace App\OurBank\Account;

use App\Bank\BankId;

class AccountId
{
    /** @var BankId */
    private $bankId;

    /** @var AccountNumber */
    private $accountNumber;

    public function __construct(BankId $bankId, AccountNumber $accountNumber)
    {
        $this->bankId        = $bankId;
        $this->accountNumber = $accountNumber;
    }

    public static function fromString(string $bankId, string $accountNumber): self
    {
        return new AccountId(new BankId($bankId), new AccountNumber($accountNumber));
    }

    public function getBankId(): BankId
    {
        return $this->bankId;
    }

    public function getAccountNumber(): AccountNumber
    {
        return $this->accountNumber;
    }

    public function getId(): string
    {
        return $this->bankId->getId().'_'.$this->accountNumber->getAccountNumber();
    }
}

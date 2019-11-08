<?php

namespace App\OurBank\Command;

use App\OurBank\Account\AccountId;

class DepositMoney
{

	/** @var AccountId */
	private $accountId;
	/** @var int */
	private $amount;

	public function __construct(AccountId $accountId, int $amount)
	{
		$this->accountId = $accountId;
		$this->amount = $amount;
	}

	public function getAccountId(): AccountId
	{
		return $this->accountId;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}


}
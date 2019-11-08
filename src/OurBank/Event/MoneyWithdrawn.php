<?php

namespace App\OurBank\Event;

use App\OurBank\Account\AccountId;

class MoneyWithdrawn
{
	/** @var AccountId */
	private $accountId;
	/** @var int */
	private $amount;
	/** @var int */
	private $balance;

	public function __construct(AccountId $accountId, int $amount,int $balance)
	{
		$this->accountId = $accountId;
		$this->amount = $amount;
		$this->balance = $balance;
	}

	public function getAccountId(): AccountId
	{
		return $this->accountId;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function getBalance(): int
	{
		return $this->balance;
	}


}
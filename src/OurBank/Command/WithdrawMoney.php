<?php

namespace App\OurBank\Command;

use App\OurBank\Account\AccountId;
use App\OurBank\Customer\CustomerId;

class WithdrawMoney
{

	/** @var AccountId */
	private $accountId;
	/** @var int */
	private $amount;
	/** @var CustomerId */
	private $initiatedBy;

	public function __construct(AccountId $accountId, int $amount, CustomerId $initiatedBy)
	{
		$this->accountId = $accountId;
		$this->amount = $amount;
		$this->initiatedBy = $initiatedBy;
	}

	public function getAccountId(): AccountId
	{
		return $this->accountId;
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function getInitiatedBy(): CustomerId
	{
		return $this->initiatedBy;
	}


}
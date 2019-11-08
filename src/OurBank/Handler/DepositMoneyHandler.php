<?php

namespace App\OurBank\Handler;

use App\OurBank\Account\Account;
use App\OurBank\Account\Accounts;
use App\OurBank\Command\DepositMoney;

class DepositMoneyHandler
{

	public function __construct()
	{
		$this->accounts = new Accounts();
	}

	public function handle(DepositMoney $command)
	{
		$account = $this->accounts->load($command->getAccountId());

		if (null === $account) {
			throw new \Exception('Unknown account:'.$command->getAccountId()->getId());
		}

		$account->deposit($command->getAmount());
		$this->accounts->save($account);
	}

}
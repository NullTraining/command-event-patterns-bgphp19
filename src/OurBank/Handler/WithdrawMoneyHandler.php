<?php

namespace App\OurBank\Handler;

use App\OurBank\Account\Account;
use App\OurBank\Account\Accounts;
use App\OurBank\Command\WithdrawMoney;
use App\OurBank\Event\MoneyWithdrawn;
use SimpleBus\SymfonyBridge\Bus\EventBus;

class WithdrawMoneyHandler
{

	public function __construct(EventBus $eventBus)
	{
		$this->accounts = new Accounts();
		$this->eventBus = $eventBus;
	}

	public function handle(WithdrawMoney $command)
	{
		$account = $this->accounts->load($command->getAccountId());

		if (null === $account) {
			throw new \Exception('Unknown account:'.$command->getAccountId()->getId());
		}

		if ($command->getInitiatedBy()->getId() !== $account->getCustomerId()->getId()) {
			throw new \Exception('Customer doesnt own the from account');
		}

		if (false === $account->canWithdraw($command->getAmount())) {
			throw new \Exception('Not enough money');
		}

		$account->withdraw($command->getAmount());
		$this->accounts->save($account);


		$withdrawn = new MoneyWithdrawn($command->getAccountId(),$command->getAmount(),$account->getBalance());

		$this->eventBus->handle($withdrawn);

	}

}
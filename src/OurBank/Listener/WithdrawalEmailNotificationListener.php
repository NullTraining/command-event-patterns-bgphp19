<?php

namespace App\OurBank\Listener;

use App\OurBank\Account\Account;
use App\OurBank\Account\Accounts;
use App\OurBank\Customer\Customer;
use App\OurBank\Customer\CustomerId;
use App\OurBank\Customer\Customers;
use App\OurBank\Event\MoneyWithdrawn;
use EmailSDK\EmailSender;

class WithdrawalEmailNotificationListener
{
	public function __construct()
	{
		$this->accounts = new Accounts();
		$this->customers = new Customers();
		$this->emailSender = new EmailSender();
	}

	public function onMoneyWithdrawn(MoneyWithdrawn $moneyWithdrawn)
	{

		$account = $this->accounts->load($moneyWithdrawn->getAccountId());

		$subject = 'New withdrawal ...';
		$body = ' Hi, We want to tell you that ...';

		$customer = $this->loadCustomer($account->getCustomerId());
		$this->emailSender->send('bank@bank.com', $customer->getEmailAddressAsString(), $subject, $body);
	}

	private function loadCustomer(CustomerId $customerId): Customer
	{
		$customer = $this->customers->load($customerId);
		if (null === $customer) {
			throw new \Exception('Unknown customerId:'.$customerId->getId());
		}

		return $customer;
	}

}
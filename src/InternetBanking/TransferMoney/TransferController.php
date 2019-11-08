<?php

declare(strict_types=1);

namespace App\InternetBanking\TransferMoney;

use App\OurBank\Account\Account;
use App\OurBank\Account\AccountId;
use App\OurBank\Account\Accounts;
use App\OurBank\Command\DepositMoney;
use App\OurBank\Command\WithdrawMoney;
use App\OurBank\Customer\Customer;
use App\OurBank\Customer\CustomerId;
use App\OurBank\Customer\Customers;
use EmailSDK\EmailSender;
use InterBankSDK\InterBankClient;
use ShortMessageServiceSDK\SmsSender;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransferController extends AbstractController
{
    /** @var Accounts */
    private $accounts;

    /** @var Customers */
    private $customers;

    /** @var EmailSender */
    private $emailSender;

    /** @var SmsSender */
    private $smsSender;

    /** @var InterBankClient */
    private $interBankClient;

    public function __construct(CommandBus $commandBus)
    {
        $this->accounts        = new Accounts();
        $this->customers       = new Customers();
        $this->emailSender     = new EmailSender();
        $this->smsSender       = new SmsSender();
        $this->interBankClient = new InterBankClient();
        $this->commandBus = $commandBus;
    }

    public function transfer(Request $request): Response
    {
        $query      = $request->query;
        $customerId = new CustomerId($query->get('customerId'));
        $sourceId   = AccountId::fromString('ABC', $query->get('from'));
        $targetId   = AccountId::fromString('ABC', $query->get('to'));
        $amount     = $query->getInt('amount');

        $withdraw = new WithdrawMoney($sourceId,$amount,$customerId);
	    $deposit = new DepositMoney($targetId,$amount);

        $this->commandBus->handle($withdraw);
        $this->commandBus->handle($deposit);

        return new Response('OK');
    }

	public function outgoingExternalTransfer(Request $request): Response
    {
        $query      = $request->query;
        $customerId = new CustomerId($query->get('customerId'));
        $sourceId   = AccountId::fromString('ABC', $query->get('from'));
        $targetId   = AccountId::fromString('ABC', $query->get('to'));
        $amount     = $query->getInt('amount');


	    $withdraw = new WithdrawMoney($sourceId,$amount,$customerId);
		$notifyOtherBank = '..';

	    $this->commandBus->handle($withdraw);
	    $this->commandBus->handle($notifyOtherBank);

        return new Response('OK');
    }

    public function incomingExternalTransfer(Request $request): Response
    {
        $query         = $request->query;
        $transactionId = $query->get('transactionId');
        $sourceId      = AccountId::fromString('ABC', $query->get('from'));
        $targetId      = AccountId::fromString('ABC', $query->get('to'));
        $amount        = $query->getInt('amount');


	    $deposit = new DepositMoney($targetId,$amount);
	    $confirm = '..';

	    $this->commandBus->handle($deposit);
	    $this->commandBus->handle($confirm);

        return new Response('OK');
    }

    private function loadAccount(AccountId $accountId): Account
    {
        $account = $this->accounts->load($accountId);
        if (null === $account) {
            throw new \Exception('Unknown account:'.$accountId->getId());
        }

        return $account;
    }

    private function loadCustomer(CustomerId $customerId): Customer
    {
        $customer = $this->customers->load($customerId);
        if (null === $customer) {
            throw new \Exception('Unknown customerId:'.$customerId->getId());
        }

        return $customer;
    }



    private function deposit(Account $account, int $amount): void
    {
        $account->deposit($amount);

        $this->accounts->save($account);
    }

    private function sendEmailAboutDeposit(Account $account, int $amount): void
    {
        $subject = 'You just received ...';
        $body    = ' Hi, We want to tell you that ...';

        $customer = $this->loadCustomer($account->getCustomerId());

        $this->emailSender->send('bank@bank.com', $customer->getEmailAddressAsString(), $subject, $body);
    }

    private function sendSmsAboutDeposit(Account $account, int $amount): void
    {
        $message  = 'You just got money...';
        $customer = $this->loadCustomer($account->getCustomerId());
        $this->smsSender->send($customer->getPhoneNumberAsString(), $message);
    }


    private function notifyReceivingBank(AccountId $accountId, int $amount): void
    {
        $this->interBankClient->send('transaction-id', $accountId->getId(), $amount);
    }

    private function confirmTransaction(string $transactionId, AccountId $accountId, int $amount): void
    {
        $this->interBankClient->confirm($transactionId, $accountId->getId(), $amount);
    }
}

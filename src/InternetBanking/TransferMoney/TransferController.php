<?php

declare(strict_types=1);

namespace App\InternetBanking\TransferMoney;

use App\OurBank\Account\Account;
use App\OurBank\Account\AccountId;
use App\OurBank\Account\Accounts;
use App\OurBank\Customer\Customer;
use App\OurBank\Customer\CustomerId;
use App\OurBank\Customer\Customers;
use EmailSDK\EmailSender;
use InterBankSDK\InterBankClient;
use ShortMessageServiceSDK\SmsSender;
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

    public function __construct()
    {
        $this->accounts        = new Accounts();
        $this->customers       = new Customers();
        $this->emailSender     = new EmailSender();
        $this->smsSender       = new SmsSender();
        $this->interBankClient = new InterBankClient();
    }

    public function transfer(Request $request): Response
    {
        $query      = $request->query;
        $customerId = new CustomerId($query->get('customerId'));
        $sourceId   = AccountId::fromString('ABC', $query->get('from'));
        $targetId   = AccountId::fromString('ABC', $query->get('to'));
        $amount     = $query->getInt('amount');

        $source = $this->loadAccount($sourceId);
        $target = $this->loadAccount($targetId);

        $this->guardCurrentUserOwnsAccount($customerId, $source);
        $this->guardEnoughMoneyOnSourceAccount($source, $amount);

        $this->withdraw($source, $amount);
        $this->deposit($target, $amount);

        $this->sendEmailAboutDeposit($target, $amount);
        $this->sendSmsAboutDeposit($target, $amount);
        $this->sendEmailAboutWithdrawal($source, $amount);

        return new Response('OK');
    }

    public function outgoingExternalTransfer(Request $request): Response
    {
        $query      = $request->query;
        $customerId = new CustomerId($query->get('customerId'));
        $sourceId   = AccountId::fromString('ABC', $query->get('from'));
        $targetId   = AccountId::fromString('ABC', $query->get('to'));
        $amount     = $query->getInt('amount');

        $source = $this->loadAccount($sourceId);

        $this->guardCurrentUserOwnsAccount($customerId, $source);
        $this->guardEnoughMoneyOnSourceAccount($source, $amount);

        $this->withdraw($source, $amount);
        $this->notifyReceivingBank($targetId, $amount);

        $this->sendEmailAboutWithdrawal($source, $amount);

        return new Response('OK');
    }

    public function incomingExternalTransfer(Request $request): Response
    {
        $query         = $request->query;
        $transactionId = $query->get('transactionId');
        $sourceId      = AccountId::fromString('ABC', $query->get('from'));
        $targetId      = AccountId::fromString('ABC', $query->get('to'));
        $amount        = $query->getInt('amount');

        $target = $this->loadAccount($targetId);

        $this->deposit($target, $amount);

        $this->confirmTransaction($transactionId, $sourceId, $amount);

        $this->sendEmailAboutDeposit($target, $amount);
        $this->sendSmsAboutDeposit($target, $amount);

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

    private function guardCurrentUserOwnsAccount(CustomerId $customerId, Account $account): void
    {
        if ($customerId->getId() !== $account->getCustomerId()->getId()) {
            throw new \Exception('Customer doesnt own the from account');
        }
    }

    private function guardEnoughMoneyOnSourceAccount(Account $account, int $amount): void
    {
        if (false === $account->canWithdraw($amount)) {
            throw new \Exception('Not enough money');
        }
    }

    private function withdraw(Account $account, int $amount): void
    {
        $account->withdraw($amount);
        $this->accounts->save($account);
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

    private function sendEmailAboutWithdrawal(Account $account, int $amount): void
    {
        $subject = 'New withdrawal ...';
        $body    = ' Hi, We want to tell you that ...';

        $customer = $this->loadCustomer($account->getCustomerId());
        $this->emailSender->send('bank@bank.com', $customer->getEmailAddressAsString(), $subject, $body);
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

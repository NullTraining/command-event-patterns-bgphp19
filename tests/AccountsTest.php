<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bank\BankId;
use App\OurBank\Account\Account;
use App\OurBank\Account\AccountId;
use App\OurBank\Account\AccountNumber;
use App\OurBank\Account\Accounts;
use App\OurBank\Customer\CustomerId;
use PHPUnit\Framework\TestCase;

class AccountsTest extends TestCase
{
    /** @var Accounts */
    private $accounts;

    public function setUp(): void
    {
        parent::setUp();

        $this->accounts = new Accounts();
        $this->accounts->truncate();

        $accountId1 = AccountId::fromString('ABC', '50f4910a-660c-43ca-91b4-de88bee555d1');
        $accountId2 = AccountId::fromString('ABC', 'b72500c9-eedb-4e16-b7b1-3f4637d7c7c6');
        $accountId3 = AccountId::fromString('ABC', '3326e920-4f16-4f20-bc21-0e282a5b9b7b');

        $customerId1 = new CustomerId('35ee3ee3-d00d-445a-a7df-10f6ea043538');
        $customerId2 = new CustomerId('99d13145-00e9-4577-bfaf-bcb49b86a68d');

        $account1 = new Account($accountId1, $customerId1, 100);
        $account2 = new Account($accountId2, $customerId1, 100);
        $account3 = new Account($accountId3, $customerId2, 100);

        $this->accounts->save($account1);
        $this->accounts->save($account2);
        $this->accounts->save($account3);
    }

    public function testLoadWorks(): void
    {
        $accountId = new AccountId(new BankId('ABC'), new AccountNumber('50f4910a-660c-43ca-91b4-de88bee555d1'));

        $account = $this->accounts->load($accountId);

        self::assertInstanceOf(Account::class, $account);
    }

    public function testFindByCustomerWorks(): void
    {
        $customerId = new CustomerId('35ee3ee3-d00d-445a-a7df-10f6ea043538');

        $accounts = $this->accounts->findByCustomer($customerId);

        self::assertCount(2, $accounts);
    }
}

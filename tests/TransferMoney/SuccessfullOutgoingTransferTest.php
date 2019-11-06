<?php

declare(strict_types=1);

namespace App\Tests\TransferMoney;

use App\Generic\EmailAddress;
use App\Generic\PhoneNumber;
use App\OurBank\Account\Account;
use App\OurBank\Account\AccountId;
use App\OurBank\Account\Accounts;
use App\OurBank\Customer\Customer;
use App\OurBank\Customer\CustomerId;
use App\OurBank\Customer\Customers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SuccessfullOutgoingTransferTest extends WebTestCase
{
    /** @var Accounts */
    private $accounts;

    /** @var Customers */
    private $customers;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->customers = new Customers();
        $this->customers->truncate();

        $this->accounts = new Accounts();
        $this->accounts->truncate();

        $this->prepareFixtures();
    }

    protected function prepareFixtures(): void
    {
        $customerId1 = new CustomerId('100');

        $customer = new Customer(
            $customerId1,
            'Alex',
            'Smith',
            new EmailAddress('alex@example.com'),
            new PhoneNumber('+123 456-7890')
        );

        $this->customers->save($customer);

        $accountId1 = AccountId::fromString('ABC', '1000001');
        $accountId2 = AccountId::fromString('ABC', '1000002');

        $account1 = new Account($accountId1, $customerId1, 100);

        $this->accounts->save($account1);
    }

    public function testIt(): void
    {
        $params = [
            'customerId' => '100',
            'from'       => '1000001',
            'to'         => '1000002',
            'amount'     => 7,
        ];

        $response = $this->doGetRequest('ibanking/outgoing_external_transfer/', $params);

        self::assertEquals('OK', $response->getContent());
        self::assertEquals(200, $response->getStatusCode());

        // Assert from bank account has 93 money on it.
        $this->assertBalanceOnAccount(93, '1000001');
    }

    protected function assertBalanceOnAccount(int $amount, string $accountNumber): void
    {
        $account = $this->loadAccount($accountNumber);
        self::assertEquals($amount, $account->getBalance());
    }

    protected function loadAccount(string $accountNumber): Account
    {
        $account = $this->accounts->load(AccountId::fromString('ABC', $accountNumber));

        if (null === $account) {
            throw new \Exception('Unknown account trying to be loaded');
        }

        return $account;
    }

    protected function doGetRequest(string $url, array $params): Response
    {
        $request = Request::create($url, 'GET', $params);

        $response = self::$kernel->handle($request);

        return $response;
    }
}

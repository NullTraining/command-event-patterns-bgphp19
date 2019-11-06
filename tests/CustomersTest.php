<?php

declare(strict_types=1);

namespace App\Tests;

use App\Generic\EmailAddress;
use App\Generic\PhoneNumber;
use App\OurBank\Customer\Customer;
use App\OurBank\Customer\CustomerId;
use App\OurBank\Customer\Customers;
use PHPUnit\Framework\TestCase;

class CustomersTest extends TestCase
{
    /** @var Customers */
    private $customers;

    public function setUp(): void
    {
        parent::setUp();

        $this->customers = new Customers();
        $this->customers->truncate();
    }

    public function testSaveAndLoadWork(): void
    {
        $customerId = new CustomerId('35ee3ee3-d00d-445a-a7df-10f6ea043538');

        $customer = new Customer(
            $customerId,
            'Alex',
            'Smith',
            new EmailAddress('alex@example.com'),
            new PhoneNumber('+123 456-7890')
        );

        $this->customers->save($customer);

        $customer = $this->customers->load($customerId);

        self::assertInstanceOf(Customer::class, $customer);
    }
}

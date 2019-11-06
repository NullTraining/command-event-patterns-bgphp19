<?php

declare(strict_types=1);

namespace App\OurBank\Customer;

use App\Generic\EmailAddress;
use App\Generic\PhoneNumber;

class Customer
{
    /** @var CustomerId */
    private $customerId;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var EmailAddress */
    private $emailAddress;

    /** @var PhoneNumber */
    private $phoneNumber;

    public function __construct(
        CustomerId $customerId,
        string $firstName,
        string $lastName,
        EmailAddress $emailAddress,
        PhoneNumber $phoneNumber
    ) {
        $this->customerId   = $customerId;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->emailAddress = $emailAddress;
        $this->phoneNumber  = $phoneNumber;
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function getEmailAddressAsString(): string
    {
        return $this->emailAddress->getValue();
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getPhoneNumberAsString(): string
    {
        return $this->phoneNumber->getValue();
    }
}

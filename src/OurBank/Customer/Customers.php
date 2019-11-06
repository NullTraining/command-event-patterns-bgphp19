<?php

declare(strict_types=1);

namespace App\OurBank\Customer;

use App\Generic\EmailAddress;
use App\Generic\PhoneNumber;
use DumbJson\JsonRepository;

class Customers extends JsonRepository
{
    public function load(CustomerId $customerId): ?Customer
    {
        return $this->find($customerId->getId());
    }

    public function save(Customer $entity): void
    {
        $this->add($entity);
    }

    ///
    /// Internal methods for dumbJson
    ///

    protected function getTableName(): string
    {
        return 'customers';
    }

    /** @param Customer $entity */
    protected function serialize($entity): array
    {
        return [
            '__className'  => get_class($entity),
            'customerId'   => $entity->getCustomerId()->getId(),
            'firstName'    => $entity->getFirstName(),
            'lastName'     => $entity->getLastName(),
            'emailAddress' => $entity->getEmailAddress()->getValue(),
            'phoneNumber'  => $entity->getPhoneNumber()->getValue(),
        ];
    }

    protected static function deserialize(array $data): Customer
    {
        return new Customer(
            new CustomerId($data['customerId']),
            $data['firstName'],
            $data['lastName'],
            new EmailAddress($data['emailAddress']),
            new PhoneNumber($data['phoneNumber'])
        );
    }

    /** @param Customer $entity */
    protected function getEntityId($entity): string
    {
        return $entity->getCustomerId()->getId();
    }
}

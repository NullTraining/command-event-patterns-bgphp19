<?php

declare(strict_types=1);

namespace App\OurBank\Account;

use App\Bank\BankId;
use App\OurBank\Customer\CustomerId;
use DumbJson\JsonRepository;

class Accounts extends JsonRepository
{
    public function load(AccountId $accountId): ?Account
    {
        return $this->find($accountId->getId());
    }

    public function save(Account $entity): void
    {
        $this->add($entity);
    }

    public function findByCustomer(CustomerId $customerId): array
    {
        $results = [];

        foreach ($this->getResults() as $account) {
            if ($account->getCustomerId() == $customerId) {
                $results[] = $account;
            }
        }

        return $results;
    }

    ///
    /// Internal methods for dumbJson
    ///

    protected function getTableName(): string
    {
        return 'accounts';
    }

    /**
     * @param Account $entity
     *
     * @return array
     */
    protected function serialize($entity): array
    {
        return [
            '__className'   => get_class($entity),
            'bankId'        => $entity->getAccountId()->getBankId()->getId(),
            'accountNumber' => $entity->getAccountId()->getAccountNumber()->getAccountNumber(),
            'customerId'    => $entity->getCustomerId()->getId(),
            'balance'       => $entity->getBalance(),
        ];
    }

    protected static function deserialize(array $data): Account
    {
        return new Account(
            new AccountId(new BankId($data['bankId']), new AccountNumber($data['accountNumber'])),
            new CustomerId($data['customerId']),
            $data['balance']
        );
    }

    /** @param  Account $entity */
    protected function getEntityId($entity): string
    {
        return $entity->getAccountId()->getId();
    }
}

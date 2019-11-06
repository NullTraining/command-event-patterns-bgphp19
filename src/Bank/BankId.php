<?php

declare(strict_types=1);

namespace App\Bank;

class BankId
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isOurBank(): bool
    {
        if ('ABC' === $this->id) {
            return true;
        }

        return false;
    }
}

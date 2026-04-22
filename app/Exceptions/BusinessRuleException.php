<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    public function __construct(string $message = 'Opération non autorisée.')
    {
        parent::__construct($message);
    }
}

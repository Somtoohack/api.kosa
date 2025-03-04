<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct($message = 'Insufficient balance')
    {
        parent::__construct($message);
    }
}
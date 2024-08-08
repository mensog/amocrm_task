<?php

namespace App\Exceptions\AmoCrm;

use Exception;

class TokenNotValidException extends Exception
{
    public function __construct()
    {
        parent::__construct('AmoCRM token not valid', 500);
    }
}

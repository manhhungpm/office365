<?php

namespace App\Exceptions;

class QueryException extends AbstractException
{
    public function __construct($message = '', $code = null)
    {
        if (!$message) {
            $message = 'Query Exception';
        }

        if (!$code) {
            $code = CODE_QUERY;
        }
        parent::__construct($message, $code);
    }
}

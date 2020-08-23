<?php

namespace App\Services\Logs;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class SmsLogHandler extends AbstractProcessingHandler {

    public function __construct($level = Logger::WARNING)
    {
        parent::__construct($level);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        sendSms((string)$record['formatted']);
    }
}
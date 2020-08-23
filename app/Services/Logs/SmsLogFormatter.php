<?php

namespace App\Services\Logs;

use Illuminate\Database\QueryException;
use Monolog\Formatter\LineFormatter;

class SmsLogFormatter extends LineFormatter
{
    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);

        $appName = env('APP_NAME');

        $this->format = "[%datetime%][{$appName}][%channel%][%level_name%]: %message%\n";
    }

    public function format(array $record){
        if(array_key_exists('context', $record) && array_key_exists('exception', $record['context'])){
            $exception = $record['context']['exception'];

            if($exception instanceof QueryException){
                $record['message'] = $exception->getPrevious()->getMessage();
            }
        }

        $message = parent::format($record);

        return $message;
    }
}
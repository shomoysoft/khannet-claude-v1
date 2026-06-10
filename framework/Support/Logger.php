<?php

namespace App\Support;

class Logger
{
    private static $logFile;

    public static function init()
    {
        self::$logFile = ROOT_PATH . 'storage/logs/app.log';

        // Create directory if not exists
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Set error handler
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleError($level, $message, $file, $line)
    {
        self::log("ERROR: $message in $file:$line");
    }

    public static function handleException($exception)
    {
        self::log("EXCEPTION: " . $exception->getMessage() . " in " .
            $exception->getFile() . ":" . $exception->getLine());
    }

    public static function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents(self::$logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}

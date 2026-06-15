<?php

namespace Framework\Support;

class Logger
{
    private static string $path;

    public static function init(): void
    {
        self::$path = ROOT . '/storage/logs/app.log';

        if (!is_dir(dirname(self::$path))) {
            mkdir(dirname(self::$path), 0755, true);
        }

        set_exception_handler([static::class, 'handleException']);
    }

    public static function handleException(\Throwable $e): void
    {
        self::exception($e, 'Uncaught ' . get_class($e));
        http_response_code(500);
        echo '500 — Internal Server Error';
        exit;
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function exception(\Throwable $e, string $message = ''): void
    {
        self::error($message ?: $e->getMessage(), [
            'exception' => get_class($e),
            'file'      => $e->getFile() . ':' . $e->getLine(),
            'trace'     => $e->getTraceAsString(),
        ]);
    }

    private static function write(string $level, string $message, array $context): void
    {
        if (!isset(self::$path)) return;

        $pad  = str_repeat(' ', 11);
        $line = sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), $level, $message);

        foreach ($context as $key => $value) {
            $line .= PHP_EOL . $pad . "{$key}: " . $value;
        }

        if (PHP_SAPI !== 'cli' && isset($_SERVER['REQUEST_METHOD'])) {
            $line .= PHP_EOL . $pad . 'request: '
                  . $_SERVER['REQUEST_METHOD'] . ' '
                  . ($_SERVER['REQUEST_URI'] ?? '');
        }

        file_put_contents(self::$path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

<?php

namespace Backend;

class Logger
{
    public static function info(string $message): void
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $file = $logDir . '/app.log';
        $line = date('Y-m-d H:i:s') . " [INFO] " . $message . PHP_EOL;
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}

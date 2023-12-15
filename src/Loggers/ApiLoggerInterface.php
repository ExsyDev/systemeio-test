<?php

namespace App\Loggers;

interface ApiLoggerInterface
{
    /**
     * @param string $message
     * @return void
     */
    public function info(string $message): void;
}

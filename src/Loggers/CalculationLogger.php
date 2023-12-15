<?php

namespace App\Loggers;

use Psr\Log\LoggerInterface;

class CalculationLogger implements ApiLoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->logger->info($message);
    }
}

<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

abstract class InvalidIdentifierHandler implements InvalidIdentifierHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected function applyPenalty(int $seconds): void
    {
        sleep($seconds);
    }

    abstract protected function enabled(): bool;

    abstract protected function getPenalty(int $invalidRequestCount): int;

    abstract protected function getInvalidRequestCount(): int;

    abstract protected function setInvalidRequestCount(int $invalidRequestCount): void;

    protected function init(ContextInterface $context): void
    {
    }

    protected function finish(): void
    {
    }

    protected function handleIdentifier(ContextInterface $context, bool $invalid): void
    {
        $this->init($context);

        if (!$this->enabled()) {
            return;
        }

        $invalidRequestCount = $this->getInvalidRequestCount();
        if ($invalid) {
            ++$invalidRequestCount;
        }

        $this->setInvalidRequestCount($invalidRequestCount);

        if ($invalidRequestCount > 0) {
            $this->logger->debug('Invalid identifier causes penalty');
            $penalty = $this->getPenalty($invalidRequestCount);
            if ($penalty > 0) {
                $this->applyPenalty($penalty);
            }
        }

        $this->finish();
    }

    public function handleInvalidIdentifier(ContextInterface $context): void
    {
        $this->handleIdentifier($context, true);
    }

    public function handleValidIdentifier(ContextInterface $context): void
    {
        $this->handleIdentifier($context, false);
    }
}

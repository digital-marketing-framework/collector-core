<?php

namespace DigitalMarketingFramework\Collector\Core\InvalidIdentifier;

use DateTime;
use DigitalMarketingFramework\Collector\Core\GlobalConfiguration\Settings\BotProtectionGlobalSettings;
use DigitalMarketingFramework\Collector\Core\Model\InvalidRequest\InvalidRequestInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;

class CountingInvalidIdentifierHandler extends InvalidIdentifierHandler implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    protected BotProtectionGlobalSettings $settings;

    protected string $identifier;

    protected ?InvalidRequestInterface $invalidRequest = null;

    public function __construct(
        protected InvalidRequestStorageInterface $invalidRequestStorage,
    ) {
    }

    protected function getSettings(): BotProtectionGlobalSettings
    {
        if (!isset($this->settings)) {
            $this->settings = $this->globalConfiguration->getGlobalSettings(BotProtectionGlobalSettings::class);
        }

        return $this->settings;
    }

    protected function enabled(): bool
    {
        return $this->getSettings()->enabled();
    }

    protected function getPenalty(int $invalidRequestCount): int
    {
        $maxPenalty = $this->getSettings()->getMaxPenalty();
        $penalty = $invalidRequestCount * $this->getSettings()->getPenaltyPerAttempt();
        if ($penalty > $maxPenalty) {
            $penalty = $maxPenalty;
        }

        return $penalty;
    }

    protected function getInvalidRequestCount(): int
    {
        $this->initRecord();

        if (!$this->invalidRequest instanceof InvalidRequestInterface) {
            return 0;
        }

        if ($this->invalidRequest->isExpired($this->getSettings()->getTimeout())) {
            return 0;
        }

        return $this->invalidRequest->getCount();
    }

    protected function setInvalidRequestCount(int $invalidRequestCount): void
    {
        if ($this->identifier === '') {
            // we can't save anything if there is no identifier given
            return;
        }

        $this->initRecord();

        if ($this->invalidRequest instanceof InvalidRequestInterface) {
            if ($invalidRequestCount > 0) {
                if ($invalidRequestCount !== $this->invalidRequest->getCount()) {
                    $this->invalidRequest->setTstamp(new DateTime());
                    $this->invalidRequest->setCount($invalidRequestCount);
                    $this->invalidRequestStorage->update($this->invalidRequest);
                }
            } else {
                $this->invalidRequestStorage->remove($this->invalidRequest);
            }
        } elseif ($invalidRequestCount > 0) {
            $invalidRequest = $this->invalidRequestStorage->create();
            $invalidRequest->setIdentifier($this->identifier);
            $invalidRequest->setTstamp(new DateTime());
            $invalidRequest->setCount($invalidRequestCount);
            $this->invalidRequestStorage->add($invalidRequest);
        }
    }

    protected function init(ContextInterface $context): void
    {
        $ipAddress = $context->getIpAddress() ?? '';
        $this->identifier = $ipAddress === '' ? '' : hash('md5', $ipAddress);
    }

    protected function initRecord(): void
    {
        if (!$this->invalidRequest instanceof InvalidRequestInterface) {
            $this->invalidRequest = null;
            if ($this->identifier !== '') {
                $this->invalidRequest = $this->invalidRequestStorage->fetchByIdentifier($this->identifier);
            }
        }
    }

    public function cleanup(): void
    {
        $expireTimestamp = time() - $this->getSettings()->getTimeout();
        $results = $this->invalidRequestStorage->fetchExpired($expireTimestamp);
        foreach ($results as $result) {
            $this->invalidRequestStorage->remove($result);
        }
    }
}

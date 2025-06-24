<?php

namespace DigitalMarketingFramework\Collector\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Collector\Core\GlobalConfiguration\Schema\CollectorCoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;

class BotProtectionGlobalSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('collector-core', CollectorCoreGlobalConfigurationSchema::KEY_BOT_PROTECTION);
    }

    public function enabled(): bool
    {
        return $this->get(CollectorCoreGlobalConfigurationSchema::KEY_BOT_PROTECTION_ENABLED);
    }

    public function getTimeout(): int
    {
        return $this->get(CollectorCoreGlobalConfigurationSchema::KEY_BOT_PROTECTION_TIMEOUT);
    }

    public function getPenaltyPerAttempt(): int
    {
        return $this->get(CollectorCoreGlobalConfigurationSchema::KEY_BOT_PROTECTION_PENALTY_PER_ATTEMPT);
    }

    public function getMaxPenalty(): int
    {
        return $this->get(CollectorCoreGlobalConfigurationSchema::KEY_BOT_PROTECTION_MAX_PENALTY);
    }
}

<?php

namespace DigitalMarketingFramework\Collector\Core\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;

class CollectorCoreGlobalConfigurationSchema extends GlobalConfigurationSchema
{
    public const KEY_BOT_PROTECTION = 'botProtection';

    public const KEY_BOT_PROTECTION_ENABLED = 'enabled';

    public const DEFAULT_BOT_PROTECTION_ENABLED = true;

    public const KEY_BOT_PROTECTION_TIMEOUT = 'timeout';

    public const DEFAULT_BOT_PROTECTION_TIMEOUT = 300;

    public const KEY_BOT_PROTECTION_PENALTY_PER_ATTEMPT = 'penaltyPerAttempt';

    public const DEFAULT_BOT_PROTECTION_PENALTY_PER_ATTEMPT = 5;

    public const KEY_BOT_PROTECTION_MAX_PENALTY = 'maxPenalty';

    public const DEFAULT_BOT_PROTECTION_MAX_PENALTY = 30;

    protected ContainerSchema $botProtectionSchema;

    public function __construct()
    {
        parent::__construct();
        $this->getRenderingDefinition()->setLabel('Collector');

        $this->botProtectionSchema = $this->getBotProtectionSchema();
        $this->addProperty(static::KEY_BOT_PROTECTION, $this->botProtectionSchema);
    }

    public function getWeight(): int
    {
        return 70;
    }

    protected function getBotProtectionSchema(): ContainerSchema
    {
        $schema = new ContainerSchema();

        $enabledSchema = new BooleanSchema(static::DEFAULT_BOT_PROTECTION_ENABLED);
        $schema->addProperty(static::KEY_BOT_PROTECTION_ENABLED, $enabledSchema);

        $timeoutSchema = new IntegerSchema(static::DEFAULT_BOT_PROTECTION_TIMEOUT);
        $timeoutSchema->getRenderingDefinition()->setLabel('Timeout (in seconds)');
        $schema->addProperty(static::KEY_BOT_PROTECTION_TIMEOUT, $timeoutSchema);

        $penaltyPerAttemptSchema = new IntegerSchema(static::DEFAULT_BOT_PROTECTION_PENALTY_PER_ATTEMPT);
        $penaltyPerAttemptSchema->getRenderingDefinition()->setLabel('Penalty per Attempt (in seconds)');
        $schema->addProperty(static::KEY_BOT_PROTECTION_PENALTY_PER_ATTEMPT, $penaltyPerAttemptSchema);

        $maxPenaltySchema = new IntegerSchema(static::DEFAULT_BOT_PROTECTION_MAX_PENALTY);
        $maxPenaltySchema->getRenderingDefinition()->setLabel('Max penalty (in seconds)');
        $schema->addProperty(static::KEY_BOT_PROTECTION_MAX_PENALTY, $maxPenaltySchema);

        return $schema;
    }
}

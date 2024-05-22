<?php

namespace DigitalMarketingFramework\Collector\Core\Api;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface CollectorRequestHandlerInterface
{
    public function getContentModifierPlugins(bool $frontend = false): array;

    public function processContentModifier(EndPointInterface $endPoint, string $plugin, string $name): array;

    public function getUserDataSets(bool $frontend = false): array;

    public function processUserData(EndPointInterface $endPoint, string $name): array;
}

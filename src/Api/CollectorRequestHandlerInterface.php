<?php

namespace DigitalMarketingFramework\Collector\Core\Api;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface CollectorRequestHandlerInterface
{
    /**
     * @return array<string,array<string,array<string>>>
     */
    public function getContentModifierPlugins(bool $frontend = false): array;

    /**
     * @return false|array<string,mixed>
     */
    public function processContentModifier(EndPointInterface $endPoint, string $plugin, string $name): array|false;

    /**
     * @return array<string,array<string>>
     */
    public function getUserDataSets(bool $frontend = false): array;

    /**
     * @return array<string,mixed>
     */
    public function processUserData(EndPointInterface $endPoint, string $name): array;
}

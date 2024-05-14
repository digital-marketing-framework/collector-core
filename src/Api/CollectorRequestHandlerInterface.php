<?php

namespace DigitalMarketingFramework\Collector\Core\Api;

interface CollectorRequestHandlerInterface
{
    public function getContentModifierPlugins(): array;

    public function processContentModifier(string $plugin, string $name): array;

    public function getUserDataSets(): array;

    public function processUserData(string $name): array;
}

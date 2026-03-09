<?php

namespace DigitalMarketingFramework\Collector\Core\ContentModifier;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface FrontendContentModifierInterface extends ContentModifierInterface
{
    /**
     * Personalized user data transformed for frontend usage
     *
     * @param array<string,mixed> $arguments
     *
     * @return array<string,mixed>|false
     */
    public function getFrontendData(DataInterface $data, array $arguments): array|false;

    /**
     * Whether to include requiredPermission in frontend settings.
     * Override and return false to opt out of client-side permission checking.
     */
    public function passPermissionRequirementsToFrontend(): bool;

    /**
     * General frontend settings for the content modifier
     *
     * @return array<string,mixed>
     */
    public function getFrontendSettings(): array;

    /**
     * Content specific frontend settings for the content modifier, derived from content specific backend settings
     *
     * @param array<string,mixed> $settings
     *
     * @return array<string,mixed>
     */
    public function getContentSpecificFrontendSettings(string $id, array $settings): array;

    public function activateFrontendScripts(): void;
}

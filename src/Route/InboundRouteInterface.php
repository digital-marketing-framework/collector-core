<?php

namespace DigitalMarketingFramework\Collector\Core\Route;

use DigitalMarketingFramework\Collector\Core\Model\Result\InboundRouteResultInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Route\RouteInterface;

interface InboundRouteInterface extends RouteInterface
{
    public const STANDARD_FIELD_GROUP = '';

    public static function getInboundRouteListLabel(): ?string;

    /**
     * @return array<string>
     */
    public function getProvidedFieldGroups(): array;

    public function addContext(ContextInterface $source, WriteableContextInterface $target): void;

    /**
     * @throws InvalidIdentifierException
     */
    public function getData(IdentifierInterface $identifier): ?InboundRouteResultInterface;

    public function getCacheTimeoutInSeconds(): ?int;
}

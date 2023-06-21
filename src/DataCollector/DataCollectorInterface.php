<?php

namespace DigitalMarketingFramework\Collector\Core\DataCollector;

use DigitalMarketingFramework\Collector\Core\Model\Result\DataCollectorResultInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface DataCollectorInterface extends PluginInterface
{
    public function addContext(ContextInterface $source, WriteableContextInterface $target): void;

    /**
     * @throws InvalidIdentifierException
     */
    public function getData(IdentifierInterface $identifier): ?DataCollectorResultInterface;

    public static function getSchema(): SchemaInterface;
}

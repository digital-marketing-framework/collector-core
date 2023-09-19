<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Result;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

interface DataCollectorResultInterface
{
    public function getData(): ?DataInterface;

    public function setData(?DataInterface $data): void;

    /**
     * @return array<IdentifierInterface>
     */
    public function getIdentifiers(): array;

    /**
     * @param array<IdentifierInterface> $identifiers
     */
    public function setIdentifiers(array $identifiers): void;
}

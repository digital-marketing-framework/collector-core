<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Result;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataCollectorResultInterface
{
    public function getData(): ?DataInterface;
    public function setData(?DataInterface $data): void;
    public function getIdentifiers(): array;
    public function setIdentifiers(array $identifiers): void;
}

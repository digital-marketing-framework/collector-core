<?php

namespace DigitalMarketingFramework\Collector\Core\Model\Result;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

class DataCollectorResult implements DataCollectorResultInterface
{
    /**
     * @param array<IdentifierInterface> $identifiers
     */
    public function __construct(
        protected ?DataInterface $data,
        protected array $identifiers,
    ) {
    }

    public function getData(): ?DataInterface
    {
        return $this->data;
    }

    public function setData(?DataInterface $data): void
    {
        $this->data = $data;
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function setIdentifiers(array $identifiers): void
    {
        $this->identifiers = $identifiers;
    }
}

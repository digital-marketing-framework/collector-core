<?php

namespace DigitalMarketingFramework\Collector\Core\DataTransformation;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataTransformationInterface
{
    public function allowed(): bool;
    public function transform(DataInterface $data): DataInterface;
}

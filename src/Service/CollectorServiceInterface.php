<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Request\RequestInterface;

interface CollectorServiceInterface
{
    public function merge(array ...$dataSets): DataInterface;
    public function collectData(RequestInterface $request): DataInterface;
}

<?php

namespace DigitalMarketingFramework\Collector\Core\Registry;

use DigitalMarketingFramework\Collector\Core\Registry\Plugin\CollectorRegistryInterface;
use DigitalMarketingFramework\Core\Cache\CacheInterface;
use DigitalMarketingFramework\Core\Cache\NonPersistentCache;
use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Log\NullLoggerFactory;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Request\DefaultRequest;
use DigitalMarketingFramework\Core\Request\RequestInterface;

class Registry extends CoreRegistry implements CollectorRegistryInterface, CacheRegistryInterface
{
    use RegistryTrait;

    public function __construct(
        protected LoggerFactoryInterface $loggerFactory = new NullLoggerFactory(),
        protected RequestInterface $request = new DefaultRequest(),
        protected CacheInterface $cache = new NonPersistentCache()
    ) {
    }
}

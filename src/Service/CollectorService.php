<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\CollectorRegistryInterface;
use DigitalMarketingFramework\Core\Cache\CacheInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Request\RequestInterface;

class CollectorService implements CollectorServiceInterface
{
    public function __construct(
        protected CollectorRegistryInterface $registry,
        protected CacheInterface $cache,
    ) {
    }

    public function merge(array ...$dataSets): DataInterface
    {
        $result = new Data();
        /** @var DataInterface $data */
        foreach ($dataSets as $data) {
            foreach ($data as $key => $value) {
                if ($result->fieldEmpty($key)) {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    public function collectData(RequestInterface $request): DataInterface
    {
        $result = new Data();
        $collectors = $this->registry->getAllDataCollectors();
        foreach ($collectors as $collector) {
            try {
                // TODO can collectors have multiple identifiers? visitor vs prospect?
                $dataIdentifier = $collector->getIdentifier($request);
                if ($dataIdentifier !== null) {
                    $data = $this->cache->fetch($dataIdentifier);
                    if ($data === null) {
                        $data = $collector->getData($request);
                    }
                    // TODO should a miss be saved in the cache?
                    if ($data !== null) {
                        $this->cache->store($dataIdentifier, $data);
                        $result = $this->merge($result, $data);
                    }
                }
            } catch (InvalidIdentifierException) {
                // TODO just continue with other data collectors, if this one is invalid.
                //      but how to relay this information so that bot protection can be applied?
                //      also, should we store this result in the cache?
            }
        }
        return $result;
    }
}

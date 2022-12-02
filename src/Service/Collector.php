<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\DataCollector\IdentifierInterface;
use DigitalMarketingFramework\Collector\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\Plugin\DataCollectorRegistryInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Cache\CacheInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class Collector implements CollectorInterface
{
    public function __construct(
        protected RegistryInterface $registry,
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

    protected function lookup(IdentifierInterface $identifier): ?DataInterface
    {
        $data = $this->cache->fetch($identifier->getCacheKey());
        if ($data !== null) {
            return Data::unpack($data);
        }
        return null;
    }

    protected function collectData(
        ContextInterface $context, 
        CollectorConfigurationInterface $configuration
    ): DataInterface {
        $result = new Data();
        $collectors = $this->registry->getAllDataCollectors($configuration);
        foreach ($collectors as $collector) {
            try {
                // TODO can collectors have multiple identifiers? visitor vs prospect?
                $identifier = $collector->getIdentifier($context);
                if ($identifier === null) {
                    continue;
                }

                $data = $this->lookup($identifier);
                if ($data === null) {
                    $data = $collector->getData($identifier);
                }
                
                // TODO should a "miss" be saved in the cache?
                //      - a cache miss shouldn't, obviously
                //      - but what about a "miss" coming from the collector?
                if ($data !== null) {
                    $this->cache->store($identifier->getCacheKey(), $data->pack());
                    $result = $this->merge($result, $data);
                }
            } catch (InvalidIdentifierException) {
                // NOTE: an invalid-identifier exception does not mean that there was no identifier and the user is just not identified
                //       it means that there was an identifier, which was invalid, which could be a malicious attempt to guess a session ID
                
                // TODO just continue with other data collectors, if this one is invalid.
                //      but how to relay this information so that bot protection can be applied?
                //      also, should we store this result in the cache?
                continue;
            }
        }
        return $result;
    }

    protected function mapData(DataInterface $data, array|string $dataMap, CollectorConfigurationInterface $configuration): DataInterface
    {
        $dataProcessor = $this->registry->getDataProcessor($dataMap);
        return $dataProcessor->process($data, ['configuration' => $configuration]);
    }

    public function collect(
        ContextInterface $context, 
        CollectorConfigurationInterface $configuration,
        array|string|null $dataMap = null
    ): DataInterface {
        $data = $this->collectData($context, $configuration);

        if ($dataMap !== null) {
            $data = $this->mapData($data, $dataMap, $configuration);
        }

        return $data;
    }
}

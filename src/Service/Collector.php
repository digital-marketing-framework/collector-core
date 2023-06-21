<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Result\DataCollectorResult;
use DigitalMarketingFramework\Collector\Core\Model\Result\DataCollectorResultInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareTrait;
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;
use DigitalMarketingFramework\Core\Context\WriteableContext;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Utility\CacheUtility;

class Collector implements CollectorInterface, DataCacheAwareInterface, ContextAwareInterface, LoggerAwareInterface
{
    use DataCacheAwareTrait;
    use ContextAwareTrait;
    use LoggerAwareTrait;

    protected InvalidIdentifierHandlerInterface $invalidIdentifierHandler;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
        $this->invalidIdentifierHandler = $registry->getInvalidIdentifierHandler();
    }

    protected function lookup(IdentifierInterface $identifier): ?DataInterface
    {
        return $this->cache->fetch($identifier);
    }

    protected function fetch(string $keyword, IdentifierInterface $identifier, CollectorConfigurationInterface $configuration): DataCollectorResultInterface
    {
        $dataCollector = $this->registry->getDataCollector($keyword, $configuration);
        if ($dataCollector === null) {
            throw new DigitalMarketingFrameworkException(sprintf('', $keyword));
        }
        $result = $dataCollector->getData($identifier);
        // "no result" can be cached too, as empty result
        // TODO can it? should it?
        if ($result === null) {
            $result = new DataCollectorResult(new Data(), [$identifier]);
        }
        return $result;
    }

    protected function save(DataInterface $data, array $identifiers): void
    {
        $identifier = array_shift($identifiers);
        $this->cache->store($identifier, $data);
        foreach ($identifiers as $referenceIdentifier) {
            $this->cache->storeReference($referenceIdentifier, $identifier);
        }
    }

    public function collect(
        CollectorConfigurationInterface $configuration,
        ?WriteableContextInterface $preparedContext = null,
        bool $invalidIdentifierHandling = false
    ): DataInterface {
        if ($preparedContext === null) {
            $preparedContext = $this->prepareContext($configuration);
        }

        $identifierCollectors = $this->registry->getAllIdentifierCollectors($configuration);
        $invalidIdentifier = false;
        $result = new Data();
        foreach ($identifierCollectors as $identifierCollector) {
            try {
                $identifier = $identifierCollector->getIdentifier($preparedContext);
                if ($identifier === null) {
                    continue;
                }

                $identifiers = [$identifier];
                $data = $this->lookup($identifier);
                if ($data === null) {
                    $dataCollectorResult = $this->fetch($identifierCollector->getKeyword(), $identifier, $configuration);
                    $identifiers = $dataCollectorResult->getIdentifiers();
                    $data = $dataCollectorResult->getData();
                }

                if ($data !== null) {
                    $this->save($data, $identifiers);
                    $result = CacheUtility::mergeData([$result, $data], override:false);
                }

            } catch (InvalidIdentifierException $e) {
                // NOTE: an invalid-identifier exception does not mean that there was no identifier and the user is just not identified
                //       it means that there was an identifier, which was invalid, which could be a malicious attempt to guess a session ID
                $this->logger->info($e->getMessage());
                $invalidIdentifier = true;
                continue;
            }
        }

        if ($invalidIdentifierHandling) {
            if ($invalidIdentifier) {
                $this->invalidIdentifierHandler->handleInvalidIdentifier($this->context);
            } else {
                $this->invalidIdentifierHandler->handleValidIdentifier($this->context);
            }
        }

        return $result;
    }

    public function prepareContext(
        CollectorConfigurationInterface $configuration,
        ?WriteableContextInterface $context = null
    ): WriteableContextInterface {
        if ($context === null) {
            $context = new WriteableContext();
        }

        $identifierCollectors = $this->registry->getAllIdentifierCollectors($configuration);
        foreach ($identifierCollectors as $identifierCollector) {
            $identifierCollector->addContext($this->context, $context);
        }

        $collectors = $this->registry->getAllDataCollectors($configuration);
        foreach ($collectors as $collector) {
            $collector->addContext($this->context, $context);
        }

        return $context;
    }
}

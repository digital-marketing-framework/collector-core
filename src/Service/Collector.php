<?php

namespace DigitalMarketingFramework\Collector\Core\Service;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Model\Result\InboundRouteResult;
use DigitalMarketingFramework\Collector\Core\Model\Result\InboundRouteResultInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareInterface;
use DigitalMarketingFramework\Core\Cache\DataCacheAwareTrait;
use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Exception\InvalidIdentifierException;
use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;
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

    protected function fetch(string $keyword, IdentifierInterface $identifier, CollectorConfigurationInterface $configuration): InboundRouteResultInterface
    {
        $inboundRoute = $this->registry->getInboundRoute($keyword, $configuration);
        if (!$inboundRoute instanceof InboundRouteInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('inbound route "%s" not found', $keyword));
        }

        $result = $inboundRoute->getData($identifier);
        // "no result" can be cached too, as empty result
        // TODO can it? should it?
        if (!$result instanceof InboundRouteResultInterface) {
            $result = new InboundRouteResult(new Data(), [$identifier]);
        }

        return $result;
    }

    /**
     * @param array<IdentifierInterface> $identifiers
     */
    protected function save(DataInterface $data, array $identifiers, int $cacheTimeoutInSeconds): void
    {
        $identifier = array_shift($identifiers);
        $this->cache->store(
            $identifier,
            $data,
            timeoutInSeconds: $cacheTimeoutInSeconds
        );
        foreach ($identifiers as $referenceIdentifier) {
            $this->cache->storeReference(
                $referenceIdentifier,
                $identifier,
                timeoutInSeconds: $cacheTimeoutInSeconds
            );
        }
    }

    /**
     * @param array<string> $fieldGroups
     */
    protected function inboundRouteNeeded(InboundRouteInterface $inboundRoute, array $fieldGroups = []): bool
    {
        if ($fieldGroups === []) {
            return true;
        }

        $providedFieldGroups = $inboundRoute->getProvidedFieldGroups();

        return array_intersect($fieldGroups, $providedFieldGroups) !== [];
    }

    /**
     * @param array<string> $fieldGroups
     *
     * @return array<string,array{inboundRoute:InboundRouteInterface,identifierCollector:IdentifierCollectorInterface}>
     */
    protected function getInboundPlugins(CollectorConfigurationInterface $configuration, array $fieldGroups = []): array
    {
        $result = [];
        $allInboundRoutes = $this->registry->getAllInboundRoutes($configuration);
        foreach ($allInboundRoutes as $inboundRoute) {
            if (!$this->inboundRouteNeeded($inboundRoute, $fieldGroups)) {
                continue;
            }

            $keyword = $inboundRoute->getKeyword();
            $identifierCollector = $this->registry->getIdentifierCollector($keyword, $configuration);

            if (!$identifierCollector instanceof IdentifierCollectorInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('No identifier collector for inbound route with keyword "%s" found.', $keyword));
            }

            $result[$keyword] = [
                'inboundRoute' => $inboundRoute,
                'identifierCollector' => $identifierCollector,
            ];
        }

        return $result;
    }

    public function prepareContextAndCollect(
        CollectorConfigurationInterface $configuration,
        WriteableContextInterface $context,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP],
        bool $invalidIdentifierHandling = false,
    ): DataInterface {
        $this->prepareContext($configuration, $context, $fieldGroups);
        $this->registry->pushContext($context);
        $data = $this->collect($configuration, $fieldGroups, $invalidIdentifierHandling);
        $this->registry->popContext();
        return $data;
    }

    public function collect(
        CollectorConfigurationInterface $configuration,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP],
        bool $invalidIdentifierHandling = false
    ): DataInterface {
        $generalCacheTimeoutInSeconds = $configuration->getGeneralCacheTimeoutInSeconds();
        $this->cache->setTimeoutInSeconds($generalCacheTimeoutInSeconds);

        $pluginSets = $this->getInboundPlugins($configuration, $fieldGroups);
        $invalidIdentifier = false;
        $result = new Data();
        foreach ($pluginSets as $keyword => $pluginSet) {
            try {
                $identifierCollector = $pluginSet['identifierCollector'];
                $inboundRoute = $pluginSet['inboundRoute'];
                $cacheTimeoutInSeconds = $inboundRoute->getCacheTimeoutInSeconds() ?? $generalCacheTimeoutInSeconds;

                $identifier = $identifierCollector->getIdentifier();
                if (!$identifier instanceof IdentifierInterface) {
                    continue;
                }

                $identifiers = [$identifier];
                $data = $cacheTimeoutInSeconds > 0 ? $this->lookup($identifier) : null;
                if (!$data instanceof DataInterface) {
                    $inboundRouteResult = $this->fetch($keyword, $identifier, $configuration);
                    $identifiers = $inboundRouteResult->getIdentifiers();
                    $data = $inboundRouteResult->getData();
                }

                if ($data instanceof DataInterface) {
                    if ($cacheTimeoutInSeconds > 0) {
                        $this->save($data, $identifiers, $cacheTimeoutInSeconds);
                    }

                    $result = CacheUtility::mergeData([$result, $data], override: false);
                }
            } catch (InvalidIdentifierException $e) {
                $this->logger->info($e->getMessage());
                $invalidIdentifier = true;
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
        WriteableContextInterface $context,
        array $fieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP]
    ): void {
        $pluginSets = $this->getInboundPlugins($configuration, $fieldGroups);
        foreach ($pluginSets as $pluginSet) {
            $pluginSet['identifierCollector']->addContext($context);
            $pluginSet['inboundRoute']->addContext($context);
        }
    }
}

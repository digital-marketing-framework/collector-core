<?php

namespace DigitalMarketingFramework\Collector\Core\Api;

use DigitalMarketingFramework\Collector\Core\ContentModifier\FrontendContentModifierInterface;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfigurationInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Collector\Core\Route\InboundRouteInterface;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Api\ApiException;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Context\WriteableContext;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class CollectorRequestHandler implements CollectorRequestHandlerInterface, LoggerAwareInterface, EndPointStorageAwareInterface
{
    use LoggerAwareTrait;
    use EndPointStorageAwareTrait;

    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    protected CollectorInterface $collector;

    protected CollectorConfigurationInterface $collectorConfiguration;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
        $this->collector = $this->registry->getCollector();
    }

    protected function getConfiguration(EndPointInterface $endPoint): CollectorConfigurationInterface
    {
        $configStack = $this->configurationDocumentManager->getConfigurationStackFromDocument($endPoint->getConfigurationDocument());

        return new CollectorConfiguration($configStack);
    }

    protected function endPointAllowed(EndPointInterface $endPoint, bool $frontend = false): bool
    {
        return $endPoint->getEnabled() && $endPoint->getPullEnabled() && (!$frontend || $endPoint->getExposeToFrontend());
    }

    /**
     * @param array<string>|null $requiredFieldGroups
     */
    protected function collectData(CollectorConfigurationInterface $configuration, ?array $requiredFieldGroups = [InboundRouteInterface::STANDARD_FIELD_GROUP]): DataInterface
    {
        if ($requiredFieldGroups === null) {
            return new Data();
        }

        $context = new WriteableContext();
        $context->setResponsive(true);

        $data = $this->collector->prepareContextAndCollect(
            $configuration,
            $context,
            $requiredFieldGroups,
            true
        );
        $context->applyResponseData();

        return $data;
    }

    public function getContentModifierPlugins(bool $frontend = false): array
    {
        $plugins = [];
        foreach ($this->endPointStorage->getAllEndPoints() as $endPoint) {
            if (!$this->endPointAllowed($endPoint, $frontend)) {
                continue;
            }

            $configuration = $this->getConfiguration($endPoint);
            $ids = $configuration->getContentModifierIds();
            foreach ($ids as $id) {
                $plugin = $configuration->getContentModifierKeyword($id);
                $name = $configuration->getContentModifierName($id);
                $plugins[$endPoint->getName()][$plugin][] = $name;
            }
        }

        return $plugins;
    }

    public function processContentModifier(EndPointInterface $endPoint, string $plugin, string $name): array|false
    {
        if (!$this->endPointAllowed($endPoint)) {
            throw new ApiException('End point not found or disabled', 404);
        }

        $configuration = $this->getConfiguration($endPoint);
        $contentModifierId = $configuration->getContentModifierIdFromName($name);

        if ($contentModifierId === null) {
            throw new ApiException(sprintf('Content modifier "%s" unknown', $name));
        }

        $contentModifier = $this->registry->getFrontendContentModifier($configuration, $contentModifierId);

        if (!$contentModifier instanceof FrontendContentModifierInterface) {
            throw new ApiException(sprintf('Content modifier "%s" unknown', $name));
        }

        if ($contentModifier->getKeyword() !== $plugin) {
            throw new ApiException('Content modifier plugin does not match.');
        }

        try {
            $data = $this->collectData($configuration, $contentModifier->getRequiredFieldGroups());
            $arguments = $this->registry->getContext()->getRequestArguments();

            return $contentModifier->getFrontendData($data, $arguments);
        } catch (DigitalMarketingFrameworkException $e) {
            throw new ApiException($e->getMessage(), 500, $e);
        }
    }

    public function getUserDataSets(bool $frontend = false): array
    {
        $sets = [];
        foreach ($this->endPointStorage->getAllEndPoints() as $endPoint) {
            if (!$this->endPointAllowed($endPoint, $frontend)) {
                continue;
            }

            $configuration = $this->getConfiguration($endPoint);
            $items = $configuration->getDataTransformationConfigurationItems();
            foreach ($items as $item) {
                $keyword = MapUtility::getItemKey($item);
                $transformation = $this->registry->getDataTransformation($keyword, $configuration, true);
                if ($transformation->allowed()) {
                    $sets[$endPoint->getName()][] = $keyword;
                }
            }
        }

        return $sets;
    }

    public function processUserData(EndPointInterface $endPoint, string $name): array
    {
        if (!$this->endPointAllowed($endPoint)) {
            throw new ApiException('End point not found or disabled', 404);
        }

        $configuration = $this->getConfiguration($endPoint);
        if (!$configuration->dataTransformationExists($name)) {
            throw new ApiException(sprintf('Data transformation "%s" unknown', $name));
        }

        $transformation = $this->registry->getDataTransformation($name, $configuration, true);

        if (!$transformation->allowed()) {
            throw new ApiException('Not allowed', 401);
        }

        try {
            // TODO field groups configurable in data transformation definition/configuration?
            $data = $this->collectData($configuration);
            $data = $transformation->transform($data);
        } catch (DigitalMarketingFrameworkException $e) {
            throw new ApiException($e->getMessage(), 500, $e);
        }

        return GeneralUtility::castDataToArray($data);
    }
}

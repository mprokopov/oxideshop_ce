<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModuleSmartyPluginsDataProvider implements SmartyPluginsDataProviderInterface
{
    /**
     * @var SmartyPluginsDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ShopConfigurationDaoBridgeInterface
     */
    private $configurationDao;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * @var ModulePathResolverInterface
     */
    private $modulePathResolver;

    public function __construct(
        SmartyPluginsDataProviderInterface $dataProvider,
        ContextInterface $context,
        ShopConfigurationDaoBridgeInterface $configurationDao,
        ModuleStateServiceInterface $moduleStateService,
        ModulePathResolverInterface $modulePathResolver
    )
    {
        $this->dataProvider = $dataProvider;
        $this->context = $context;
        $this->modulePathResolver = $modulePathResolver;
        $this->moduleStateService = $moduleStateService;
        $this->configurationDao = $configurationDao;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getPlugins(): array
    {
        $pluginsPaths = $this->dataProvider->getPlugins();
        $pluginsPaths = array_merge($this->getModuleSmartyPluginDirectories(), $pluginsPaths);
        return $pluginsPaths;
    }

    /**
     * @return array
     */
    private function getModuleSmartyPluginDirectories(): array
    {
        $shopConfiguration = $this->configurationDao->get();
        $shopId = $this->context->getCurrentShopId();

        $smartyPluginsDirectories = [];
        foreach ($shopConfiguration->getModuleConfigurations() as $configuration) {
            if ($this->canAddDirectories($configuration, $shopId)) {
                $directories = $configuration->getSmartyPluginDirectories();
                $fullPathToModule = $this->getModuleFullPath($configuration, $shopId);
                foreach ($directories as $directory) {
                    $smartyPluginsDirectories[] = $fullPathToModule . DIRECTORY_SEPARATOR . $directory->getDirectory();
                }
            }
        }
        return $smartyPluginsDirectories;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @return bool
     */
    private function canAddDirectories(ModuleConfiguration $configuration, int $shopId): bool
    {
        return $configuration->hasSmartyPluginDirectories() &&
            $this->moduleStateService->isActive($configuration->getId(), $shopId);
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @return string
     */
    private function getModuleFullPath(ModuleConfiguration $configuration, int $shopId): string
    {
        return $this->modulePathResolver->getFullModulePathFromConfiguration(
            $configuration->getId(),
            $shopId
        );
    }
}

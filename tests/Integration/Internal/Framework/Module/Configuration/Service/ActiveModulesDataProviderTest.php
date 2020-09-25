<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ActiveModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private $activeModuleId = 'activeModuleId';
    private $inactiveModuleId = 'inActiveModuleId';

    public function testGetIds(): void
    {
        $this->prepareTestShopConfiguration();

        $this->assertSame(
            [$this->activeModuleId],
            $this->get(ActiveModulesDataProviderInterface::class)->getModuleIds()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setPath('some-path')
            ->setModuleSource('some-source');

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setPath('some-path')
            ->setModuleSource('some-source');

        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration
            ->addModuleConfiguration($activeModule)
            ->addModuleConfiguration($inactiveModule);

        $dao->save($shopConfiguration, 1);

        $this->get(ModuleActivationServiceInterface::class)->activate($this->activeModuleId, 1);
    }
}

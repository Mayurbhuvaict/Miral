<?php

namespace NzzFlyoutMenu;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use NzzFlyoutMenu\Installer\CustomFieldInstaller;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class NzzFlyoutMenu extends Plugin
{
    public function install(InstallContext $context): void
    {
        (new CustomFieldInstaller($this->container))->install($context);
    }
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }
        (new CustomFieldInstaller($this->container))->uninstall($uninstallContext);
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);


        if (method_exists($connection, 'executeStatement')) {
            $connection->executeStatement('DROP TABLE nzz_dynamic_group');


            return;
        }

        if (method_exists($connection, 'exec')) {
            /** @noinspection PhpDeprecationInspection */
            $connection->exec('DROP TABLE nzz_dynamic_group');

        }
    }

}


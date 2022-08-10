<?php declare(strict_types=1);

namespace KohlKramer2022;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Storefront\Framework\ThemeInterface;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;


class KohlKramer2022 extends Plugin implements ThemeInterface
{
    public function uninstall(UninstallContext $context): void
    {

        if ($context->keepUserData()) {
            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);


        if (method_exists($connection, 'executeStatement')) {
            $connection->executeStatement('DROP TABLE KohlKramer_home_slider_translation');
            $connection->executeStatement('DROP TABLE KohlKramer_home_slider');

            return;
        }

        if (method_exists($connection, 'exec')) {
            /** @noinspection PhpDeprecationInspection */
            $connection->exec('DROP TABLE KohlKramer_home_slider_translation');
            $connection->exec('DROP TABLE KohlKramer_home_slider');
        }

    }
}

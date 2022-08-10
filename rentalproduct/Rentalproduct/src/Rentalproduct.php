<?php declare(strict_types=1);

namespace Rentalproduct;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class Rentalproduct extends Plugin
{
    public function uninstall(UninstallContext $context): void
    {

        if ($context->keepUserData()) {
            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);


        if (method_exists($connection, 'executeStatement')) {
            $connection->executeStatement('DROP TABLE rental_product');
            return;
        }

        if (method_exists($connection, 'exec')) {
            /** @noinspection PhpDeprecationInspection */
            $connection->exec('DROP TABLE rental_product');
        }

    }
}

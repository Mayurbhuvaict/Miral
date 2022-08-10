<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class ICTECHLimitedLoginAttempts extends Plugin
{
    public function uninstall(UninstallContext $context): void
    {

        if ($context->keepUserData()) {
            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        if (method_exists($connection, 'executeStatement')) {
            $connection->executeStatement('DROP TABLE ictech_limited_login_attempts');
            return;
        }

        if (method_exists($connection, 'exec')) {
            /** @noinspection PhpDeprecationInspection */
            $connection->exec('DROP TABLE ictech_limited_login_attempts');
        }
    }
}

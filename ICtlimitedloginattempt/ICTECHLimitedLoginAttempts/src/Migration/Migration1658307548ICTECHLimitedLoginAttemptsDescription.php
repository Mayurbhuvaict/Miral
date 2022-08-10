<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1658307548ICTECHLimitedLoginAttemptsDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1658307548;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
        CREATE TABLE `ictech_limited_login_attempts` (
        `id` BINARY(16) NOT NULL,
        `customer_id` BINARY(16) NOT NULL,
        `attempt` INT(11) NULL,
        `attempt_lockout` INT(11) NULL,
        `created_at` DATETIME(3) NOT NULL,
        `updated_at` DATETIME(3) NULL,
        PRIMARY KEY (`id`),
        KEY `fk.ictech_limited_login_attempts.customer_id` (`customer_id`),
        CONSTRAINT `fk.ictech_limited_login_attempts.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}

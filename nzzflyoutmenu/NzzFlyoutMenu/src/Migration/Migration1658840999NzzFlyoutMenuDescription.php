<?php declare(strict_types=1);

namespace NzzFlyoutMenu\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1658840999NzzFlyoutMenuDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1658840999;
    }

    public function update(Connection $connection): void
    {
        // implement update

        $connection->executeStatement("CREATE TABLE `nzz_dynamic_group` (
    `id` BINARY(16) NOT NULL,
    `category_id` BINARY(16) NOT NULL,
    `category_version_id` BINARY(16) NOT NULL,
    `product_stream_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.nzz_dynamic_group.product_stream_id` (`product_stream_id`),
    CONSTRAINT `fk.nzz_dynamic_group.product_stream_id` FOREIGN KEY (`product_stream_id`) REFERENCES `product_stream` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}

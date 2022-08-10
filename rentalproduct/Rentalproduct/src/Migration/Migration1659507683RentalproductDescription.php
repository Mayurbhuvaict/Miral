<?php declare(strict_types=1);

namespace Rentalproduct\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1659507683RentalproductDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659507683;
    }

    public function update(Connection $connection): void
    {
        // implement update
            $connection->executeStatement("CREATE TABLE `rental_product` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL,
    `rule_id` BINARY(16) NOT NULL,
    `day_start` INT(11) NOT NULL,
    `day_end` INT(11) NULL,
    `custom_fields` JSON NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.rental_product.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
    KEY `fk.rental_product.product_id` (`product_id`,`product_version_id`),
    KEY `fk.rental_product.rule_id` (`rule_id`),
    CONSTRAINT `fk.rental_product.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.rental_product.rule_id` FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}

<?php declare(strict_types=1);

namespace KohlKramer2022\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1657718956KohlKramerHomeSliderDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1657718956;
    }

    public function update(Connection $connection): void
    {
        // implement update
        // implement update
        $connection->executeStatement('
        CREATE TABLE `KohlKramer_home_slider` (
    `id` BINARY(16) NOT NULL,
    `media_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.KohlKramer_home_slider.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ');

        $connection->executeStatement('
        CREATE TABLE `KohlKramer_home_slider_translation` (
    `title` VARCHAR(255) NOT NULL,
    `description` LONGTEXT NULL,
    `link_text` VARCHAR(255) NULL,
    `link` VARCHAR(255) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `KohlKramer_home_slider_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`KohlKramer_home_slider_id`,`language_id`),
    KEY `fk.KohlKramer_home_slider_translation.KohlKramer_home_slider_id` (`KohlKramer_home_slider_id`),
    KEY `fk.KohlKramer_home_slider_translation.language_id` (`language_id`),
    CONSTRAINT `fk.KohlKramer_home_slider_translation.KohlKramer_home_slider_id` FOREIGN KEY (`KohlKramer_home_slider_id`) REFERENCES `KohlKramer_home_slider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.KohlKramer_home_slider_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}

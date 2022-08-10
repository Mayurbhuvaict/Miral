<?php

declare(strict_types=1);

namespace NzzFlyoutMenu\Installer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Connection;


class CustomFieldInstaller implements InstallerInterface
{
    public const NZZ_POSITION_CATEGORY = 'ed39626e94fd4dfe9d81976fdbcdb06d';

    /** @var EntityRepositoryInterface */
    private $customFieldRepository;

    /** @var EntityRepositoryInterface */
    private $customFieldSetRepository;

    /** @var Connection */
    private $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->customFieldSetRepository = $container->get('custom_field_set.repository');
        $this->customFieldRepository = $container->get('custom_field.repository');
        $this->connection = $container->get(Connection::class);

        $this->customFieldSets = [
            [
                'id' => self::NZZ_POSITION_CATEGORY,
                'name' => 'nzz_flyout_menu_position_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'NZZ Position field set',
                        'de-DE' => 'NZZ Position field set',
                    ],
                ],
                'relation' => [
                    'id' => '6dfd8e25dbbb41e880eeba3c7108d109',
                    'entityName' => 'category',
                ]
            ]
        ];

        $this->customFields = [
            [
                'id' => 'de5f4e10cd1a4f6e9710207638c0c0eb',
                'name' => "nzz_flyout_menu_position",
                'type' => CustomFieldTypes::SELECT,
                'customFieldSetId' => self::NZZ_POSITION_CATEGORY,
                'config' => [
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'customFieldPosition' => 1,
                    'options' => [
                        [
                            'label' => ['en-GB' => 'Right', 'de-DE' => 'Right'],
                            'value' => 'nzz-align-right',
                            'default' => 'nzz-align-right'
                        ],
                        [
                            'label' => ['en-GB' => 'Bottom', 'de-DE' => 'Bottom'],
                            'value' => 'nzz-align-bottom'
                        ]
                    ],
                    'label' => [
                        'en-GB' => 'NZZ Product Position',
                        'de-DE' => 'NZZ Product Position'
                    ],
                    'helpText' => [
                        'en-GB' => 'Required: Field to define the position of products in Flayout Menu in nevigation',
                        'de-DE' => 'Required: Field to define the position of products in Flayout Menu in nevigation'
                    ]
                ],
            ]
        ];
    }
    public function install(InstallContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->upsertCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->upsertCustomField($customField, $context->getContext());
        }
    }
    public function uninstall(UninstallContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->deleteCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->deleteCustomField($customField, $context->getContext());
        }
        foreach ($this->customFieldSets as $customFieldSet) {
            foreach ($this->customFields as $customField) {
                $this->deleteCustomFieldFromCategory($customFieldSet,$customField, $context->getContext());
            }
        }
    }
    private function upsertCustomField(array $customField, Context $context): void
    {
        $data = [
            'id' => $customField['id'],
            'name' => $customField['name'],
            'type' => $customField['type'],
            'active' => true,
            'customFieldSetId' => $customField['customFieldSetId'],
            'config' => $customField['config'],
        ];
        $this->customFieldRepository->upsert([$data], $context);
    }
    private function upsertCustomFieldSet(array $customFieldSet, Context $context): void
    {
        $data = [
            'id' => $customFieldSet['id'],
            'name' => $customFieldSet['name'],
            'config' => $customFieldSet['config'],
            'active' => true,
            'relations' => [
                [
                    'id' => $customFieldSet['relation']['id'],
                    'entityName' => $customFieldSet['relation']['entityName'],
                ],
            ],
        ];
        $this->customFieldSetRepository->upsert([$data], $context);
    }

    private function deleteCustomField(array $customField, Context $context): void
    {
        $data = [
            'id' => $customField['id'],
            'name' => $customField['name'],
            'type' => $customField['type'],
            'active' => true,
            'customFieldSetId' => $customField['customFieldSetId'],
            'config' => $customField['config'],
        ];
        $this->customFieldRepository->delete([$data], $context);
    }

    private function deleteCustomFieldSet(array $customFieldSet, Context $context): void
    {
        $data = [
            'id' => $customFieldSet['id'],
            'name' => $customFieldSet['name'],
            'config' => $customFieldSet['config'],
            'active' => true,
            'relations' => [
                [
                    'id' => $customFieldSet['relation']['id'],
                    'entityName' => $customFieldSet['relation']['entityName'],
                ],
            ],
        ];
        $this->customFieldSetRepository->delete([$data], $context);
    }
    private function deleteCustomFieldFromCategory(array $customFieldSet,array $customField, Context $context): void
    {
        $customFieldName = '"'.$customField["name"].'"';
        $query = "UPDATE `" . $customFieldSet['relation']['entityName'] ."_translation` SET `custom_fields` = JSON_REMOVE(`custom_fields`, '$.$customFieldName');";
        $this->connection->executeStatement($query);
    }
}

<?php

declare(strict_types=1);

namespace NzzFlyoutMenu\Core\Content\Extension\ProductStream;

use NzzFlyoutMenu\Core\Content\NzzDynamicGroup\NzzDynamicGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Content\ProductStream\ProductStreamDefinition;

class NzzProductStreamExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'nzzDynamicGroup',
                NzzDynamicGroupDefinition::class,
                'product_stream_id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductStreamDefinition::class;
    }
}

<?php

declare(strict_types=1);

namespace NzzFlyoutMenu\Core\Content\Extension\Category;

use NzzFlyoutMenu\Core\Content\NzzDynamicGroup\NzzDynamicGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


use Shopware\Core\Content\Category\CategoryDefinition;

class NzzCategoryExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField(
                'nzzDynamicGroupCat',
                'id',
                'category_id',
                NzzDynamicGroupDefinition::class,
                true
            ))->addFlags(new CascadeDelete())
        );
//
//        $collection->add(
//            (new OneToManyAssociationField(
//                'defaultTeamCategory',
//                EEComTeamDefinition::class,
//                'default_category_id',
//                'id'
//            ))
//        );

    }

    public function getDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }
}

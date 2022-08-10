<?php

declare(strict_types=1);

namespace Rentalproduct\Core\Content\Extension\Product;

use Rentalproduct\Core\Content\RentalProduct\RentalProductDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class RentalProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'rentalProduct',
                RentalProductDefinition::class,
                'product_id',
                'id'))->addFlags(new ApiAware(), new CascadeDelete())
        );

    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}

<?php

declare(strict_types=1);

namespace Rentalproduct\Core\Content\Extension\Rule;

use Rentalproduct\Core\Content\RentalProduct\RentalProductDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class RentalRuleExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'rentalProductRule',
                RentalProductDefinition::class,
                'rule_id',
                'id'))->addFlags(new ApiAware(), new CascadeDelete(), new Extension())
        );
    }

    public function getDefinitionClass(): string
    {
        return RuleDefinition::class;
    }
}

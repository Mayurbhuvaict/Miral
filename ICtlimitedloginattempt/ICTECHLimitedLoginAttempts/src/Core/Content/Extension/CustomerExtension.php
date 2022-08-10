<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Core\Content\Extension;


use ICTECHLimitedLoginAttempts\Core\Content\LimitedLogin\IctechLimitedLoginAttemptsDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('ictechCustomer', IctechLimitedLoginAttemptsDefinition::class, 'customer_id')
        );
    }
}

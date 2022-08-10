<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Core\Content\LimitedLogin;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class IctechLimitedLoginAttemptsDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'ictech_limited_login_attempts';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return IctechLimitedLoginAttemptsEntity::class;
    }

    public function getCollectionClass(): string
    {
        return IctechLimitedLoginAttemptsCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new Required()),
            (new IntField('attempt', 'attempt'))->addFlags(new ApiAware()),
            (new IntField('attempt_lockout', 'attemptLockout'))->addFlags(new ApiAware()),

            new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class),
        ]);
    }
}

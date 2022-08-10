<?php declare(strict_types=1);

namespace NzzFlyoutMenu\Core\Content\NzzDynamicGroup;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(NzzDynamicGroupEntity $entity)
 * @method void                set(string $key, NzzDynamicGroupEntity $entity)
 * @method NzzDynamicGroupEntity[]    getIterator()
 * @method NzzDynamicGroupEntity[]    getElements()
 * @method NzzDynamicGroupEntity|null get(string $key)
 * @method NzzDynamicGroupEntity|null first()
 * @method NzzDynamicGroupEntity|null last()
 */
class NzzDynamicGroupCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return NzzDynamicGroupEntity::class;
    }
}
<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Core\Content\LimitedLogin;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(IctechLimitedLoginAttemptsEntity $entity)
 * @method void                set(string $key, IctechLimitedLoginAttemptsEntity $entity)
 * @method IctechLimitedLoginAttemptsEntity[]    getIterator()
 * @method IctechLimitedLoginAttemptsEntity[]    getElements()
 * @method IctechLimitedLoginAttemptsEntity|null get(string $key)
 * @method IctechLimitedLoginAttemptsEntity|null first()
 * @method IctechLimitedLoginAttemptsEntity|null last()
 */
class IctechLimitedLoginAttemptsCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return IctechLimitedLoginAttemptsEntity::class;
    }
}
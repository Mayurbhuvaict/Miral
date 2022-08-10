<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\KohlKramerHomeSlider;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(KohlKramerHomeSliderEntity $entity)
 * @method void                set(string $key, KohlKramerHomeSliderEntity $entity)
 * @method KohlKramerHomeSliderEntity[]    getIterator()
 * @method KohlKramerHomeSliderEntity[]    getElements()
 * @method KohlKramerHomeSliderEntity|null get(string $key)
 * @method KohlKramerHomeSliderEntity|null first()
 * @method KohlKramerHomeSliderEntity|null last()
 */
class KohlKramerHomeSliderCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KohlKramerHomeSliderEntity::class;
    }
}
<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\KohlKramerHomeSlider\Aggregate\KohlKramerHomeSliderTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(KohlKramerHomeSliderTranslationEntity $entity)
 * @method void                set(string $key, KohlKramerHomeSliderTranslationEntity $entity)
 * @method KohlKramerHomeSliderTranslationEntity[]    getIterator()
 * @method KohlKramerHomeSliderTranslationEntity[]    getElements()
 * @method KohlKramerHomeSliderTranslationEntity|null get(string $key)
 * @method KohlKramerHomeSliderTranslationEntity|null first()
 * @method KohlKramerHomeSliderTranslationEntity|null last()
 */
class KohlKramerHomeSliderTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KohlKramerHomeSliderTranslationEntity::class;
    }
}
<?php declare(strict_types=1);

namespace  KohlKramer2022\Core\Content\KohlKramerHomeSlider\DataResolver;

use KohlKramer2022\Core\Content\KohlKramerHomeSlider\KohlKramerHomeSliderDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class SliderCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'kohlkramer-home-page-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();
        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add(
            'kohlkramer-home-page-slider',
            KohlKramerHomeSliderDefinition::class,
            $criteria
        );
        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $slot->setData($result->get('kohlkramer-home-page-slider'));
    }
}

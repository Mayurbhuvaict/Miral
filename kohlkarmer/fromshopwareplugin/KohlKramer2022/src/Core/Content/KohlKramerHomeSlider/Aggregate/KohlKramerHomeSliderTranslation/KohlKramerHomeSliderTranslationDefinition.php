<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\KohlKramerHomeSlider\Aggregate\KohlKramerHomeSliderTranslation;

use KohlKramer2022\Core\Content\KohlKramerHomeSlider\KohlKramerHomeSliderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class KohlKramerHomeSliderTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'KohlKramer_home_slider_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return KohlKramerHomeSliderTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return KohlKramerHomeSliderTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return KohlKramerHomeSliderDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new LongTextField('description', 'description'))->addFlags(new ApiAware(), new AllowHtml()),
            new StringField('link_text', 'linkText'),
            new StringField('link', 'link'),
        ]);
    }
}

<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\KohlKramerHomeSlider;

use KohlKramer2022\Core\Content\KohlKramerHomeSlider\Aggregate\KohlKramerHomeSliderTranslation\KohlKramerHomeSliderTranslationDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class KohlKramerHomeSliderDefinition extends EntityDefinition
{

    public const ENTITY_NAME = 'KohlKramer_home_slider';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return KohlKramerHomeSliderEntity::class;
    }

    public function getCollectionClass(): string
    {
        return KohlKramerHomeSliderCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new TranslatedField('title'),
            new TranslatedField('description'),
            new TranslatedField('linkText'),
            new TranslatedField('link'),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false),
            (new TranslationsAssociationField(KohlKramerHomeSliderTranslationDefinition::class, 'KohlKramer_home_slider_id'))->addFlags(new Inherited(), new Required()),
        ]);
    }
}

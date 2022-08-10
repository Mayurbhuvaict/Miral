<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\Extension;

use KohlKramer2022\Core\Content\KohlKramerHomeSlider\Aggregate\KohlKramerHomeSliderTranslation\KohlKramerHomeSliderTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;
use Shopware\Core\System\Language\LanguageDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'KohlKramerSliderTranslation',
                KohlKramerHomeSliderTranslationDefinition::class,
                'language_id',
                'id'
            ))->addFlags(new CascadeDelete(),new Extension())
        );
    }

    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}

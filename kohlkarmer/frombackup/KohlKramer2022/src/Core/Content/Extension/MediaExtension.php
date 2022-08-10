<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\Extension;

use KohlKramer2022\Core\Content\KohlKramerHomeSlider\KohlKramerHomeSliderDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Extension;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'kohlkramerSlider',
                KohlKramerHomeSliderDefinition::class,
                'media_id'
            ))->addFlags(new CascadeDelete(), new Extension())
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}

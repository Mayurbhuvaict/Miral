<?php declare(strict_types=1);

namespace KohlKramer2022\Core\Content\KohlKramerHomeSlider\Aggregate\KohlKramerHomeSliderTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use KohlKramer2022\Core\Content\KohlKramerHomeSlider\KohlKramerHomeSliderEntity;
use Shopware\Core\System\Language\LanguageEntity;

class KohlKramerHomeSliderTranslationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $linkText;

    /**
     * @var string|null
     */
    protected $link;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $kohlKramerHomeSliderId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var KohlKramerHomeSliderEntity|null
     */
    protected $kohlKramerHomeSlider;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkText(?string $linkText): void
    {
        $this->linkText = $linkText;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getKohlKramerHomeSliderId(): string
    {
        return $this->kohlKramerHomeSliderId;
    }

    public function setKohlKramerHomeSliderId(string $kohlKramerHomeSliderId): void
    {
        $this->kohlKramerHomeSliderId = $kohlKramerHomeSliderId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getKohlKramerHomeSlider(): ?KohlKramerHomeSliderEntity
    {
        return $this->kohlKramerHomeSlider;
    }

    public function setKohlKramerHomeSlider(?KohlKramerHomeSliderEntity $kohlKramerHomeSlider): void
    {
        $this->kohlKramerHomeSlider = $kohlKramerHomeSlider;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}

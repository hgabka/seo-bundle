<?php

namespace Hgabka\SeoBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\MediaBundle\Entity\Media;
use Hgabka\SeoBundle\Form\SeoType;
use Hgabka\SeoBundle\Repository\SeoRepository;
use Hgabka\UtilsBundle\Entity\EntityInterface;
use Hgabka\UtilsBundle\Helper\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seo metadata for entities.
 *
 * @ORM\Entity(repositoryClass="Hgabka\SeoBundle\Repository\SeoRepository")
 * @ORM\Table(name="hg_seo_seo", indexes={@ORM\Index(name="idx_seo_lookup", columns={"ref_id", "ref_entity_name"})})
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
#[ORM\Entity(repositoryClass: SeoRepository::class)]
#[ORM\Table(name: 'hg_seo_seo')]
#[ORM\Index(name: 'idx_seo_lookup', columns: ['ref_id', 'ref_entity_name'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Seo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", nullable=true)
     * @Assert\Length(max=65)
     */
    #[ORM\Column(name: 'meta_title', type: 'string', nullable: true)]
    #[Assert\Length(max: 65)]
    protected ?string $metaTitle = null;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     * @Assert\Length(max=155)
     */
    #[ORM\Column(name: 'meta_description', type: 'text', nullable: true)]
    #[Assert\Length(max: 155)]
    protected ?string $metaDescription = null;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_author", type="string", nullable=true)
     */
    #[ORM\Column(name: 'meta_author', type: 'string', nullable: true)]
    protected ?string $metaAuthor = null;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_robots", type="string", nullable=true)
     */
    #[ORM\Column(name: 'meta_robots', type: 'string', nullable: true)]
    protected ?string $metaRobots = null;

    /**
     * @var string
     *
     * @ORM\Column(name="og_type", type="string", nullable=true)
     */
    #[ORM\Column(name: 'og_type', type: 'string', nullable: true)]
    protected ?string $ogType = null;

    /**
     * @var string
     *
     * @ORM\Column(name="og_title", type="string", nullable=true)
     */
    #[ORM\Column(name: 'og_title', type: 'string', nullable: true)]
    protected ?string $ogTitle = null;

    /**
     * @var string
     *
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'og_description', type: 'text', nullable: true)]
    protected ?string $ogDescription = null;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="og_image_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'og_image_id', referencedColumnName: 'id')]
    protected ?Media $ogImage = null;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_metadata", type="text", nullable=true)
     */
    #[ORM\Column(name: 'extra_metadata', type: 'text', nullable: true)]
    protected ?string $extraMetadata = null;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", name="ref_id", nullable=true)
     */
    #[ORM\Column(name: 'ref_id', type: 'bigint', nullable: true)]
    protected ?int $refId = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="ref_entity_name", nullable=true)
     */
    #[ORM\Column(name: 'ref_entity_name', type: 'string', nullable: true)]
    protected ?string $refEntityName = null;

    /**
     * @ORM\Column(type="string", nullable=true, name="og_url")
     */
    #[ORM\Column(name: 'og_url', type: 'string', nullable: true)]
    protected ?string $ogUrl = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_author")
     */
    #[ORM\Column(name: 'og_article_author', type: 'string', length: 100, nullable: true)]
    protected ?string $ogArticleAuthor = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_publisher")
     */
    #[ORM\Column(name: 'og_article_publisher', type: 'string', length: 100, nullable: true)]
    protected ?string $ogArticlePublisher = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, name="og_article_section")
     */
    #[ORM\Column(name: 'og_article_section', type: 'string', length: 100, nullable: true)]
    protected ?string $ogArticleSection = null;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_title", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_title', type: 'string', length: 255, nullable: true)]
    protected ?string $twitterTitle = null;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_description", type="text", nullable=true)
     */
    #[ORM\Column(name: 'twitter_description', type: 'text', nullable: true)]
    protected ?string $twitterDescription = null;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_site", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_site', type: 'string', length: 255, nullable: true)]
    protected ?string $twitterSite = null;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_creator", type="string", length=255, nullable=true)
     */
    #[ORM\Column(name: 'twitter_creator', type: 'string', length: 255, nullable: true)]
    protected ?string $twitterCreator = null;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Hgabka\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="twitter_image_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'twitter_image_id', referencedColumnName: 'id')]
    protected ?Media $twitterImage = null;

    /**
     * Return string representation of entity.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'hg_seo.admin.label.seo';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return Seo
     */
    public function setOgUrl(?string $url): static
    {
        $this->ogUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getOgUrl(): ?string
    {
        return $this->ogUrl;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $title
     *
     * @return string
     */
    public function setMetaTitle(?string $title): static
    {
        $this->metaTitle = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaAuthor(): ?string
    {
        return $this->metaAuthor;
    }

    /**
     * @param string $meta
     *
     * @return Seo
     */
    public function setMetaAuthor(?string $meta): static
    {
        $this->metaAuthor = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $meta
     *
     * @return Seo
     */
    public function setMetaDescription(?string $meta): static
    {
        $this->metaDescription = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaRobots(): ?string
    {
        return $this->metaRobots;
    }

    /**
     * @param string $meta
     *
     * @return Seo
     */
    public function setMetaRobots(?string $meta): static
    {
        $this->metaRobots = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtraMetadata(): ?string
    {
        return $this->extraMetadata;
    }

    /**
     * @param string $extraMetadata
     *
     * @return Seo
     */
    public function setExtraMetadata(?string $extraMetadata): static
    {
        $this->extraMetadata = $extraMetadata;

        return $this;
    }

    /**
     * @param string $ogDescription
     *
     * @return Seo
     */
    public function setOgDescription(?string $ogDescription): static
    {
        $this->ogDescription = $ogDescription;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    /**
     * @param Media $ogImage
     *
     * @return Seo
     */
    public function setOgImage(?Media $ogImage = null): static
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgImage(): ?Media
    {
        return $this->ogImage;
    }

    /**
     * @param string $ogTitle
     *
     * @return Seo
     */
    public function setOgTitle(?string $ogTitle): static
    {
        $this->ogTitle = $ogTitle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    /**
     * @param string $ogType
     *
     * @return Seo
     */
    public function setOgType(?string $ogType): static
    {
        $this->ogType = $ogType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgType(): ?string
    {
        return $this->ogType;
    }

    /**
     * @return mixed
     */
    public function getOgArticleAuthor(): ?string
    {
        return $this->ogArticleAuthor;
    }

    /**
     * @param mixed $ogArticleAuthor
     */
    public function setOgArticleAuthor(?string $ogArticleAuthor): static
    {
        $this->ogArticleAuthor = $ogArticleAuthor;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgArticlePublisher(): ?string
    {
        return $this->ogArticlePublisher;
    }

    /**
     * @param mixed $ogArticlePublisher
     */
    public function setOgArticlePublisher(?string $ogArticlePublisher): static
    {
        $this->ogArticlePublisher = $ogArticlePublisher;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOgArticleSection(): ?string
    {
        return $this->ogArticleSection;
    }

    /**
     * @param mixed $ogArticleSection
     */
    public function setOgArticleSection(?string $ogArticleSection): static
    {
        $this->ogArticleSection = $ogArticleSection;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterTitle(): ?string
    {
        return $this->twitterTitle;
    }

    /**
     * @param string $twitterTitle
     */
    public function setTwitterTitle(?string $twitterTitle): static
    {
        $this->twitterTitle = $twitterTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterDescription(): ?string
    {
        return $this->twitterDescription;
    }

    /**
     * @param string $twitterDescription
     */
    public function setTwitterDescription(?string $twitterDescription): static
    {
        $this->twitterDescription = $twitterDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterSite(): ?string
    {
        return $this->twitterSite;
    }

    /**
     * @param string $twitterSite
     */
    public function setTwitterSite(?string $twitterSite): static
    {
        $this->twitterSite = $twitterSite;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterCreator(): ?string
    {
        return $this->twitterCreator;
    }

    /**
     * @param string $twitterCreator
     */
    public function setTwitterCreator(?string $twitterCreator): static
    {
        $this->twitterCreator = $twitterCreator;

        return $this;
    }

    /**
     * @return Media
     */
    public function getTwitterImage(): ?Media
    {
        return $this->twitterImage;
    }

    /**
     * @param Media $twitterImage
     */
    public function setTwitterImage(?Media $twitterImage): static
    {
        $this->twitterImage = $twitterImage;

        return $this;
    }

    /**
     * Get refId.
     *
     * @return int
     */
    public function getRefId(): ?int
    {
        return $this->refId;
    }

    /**
     * Get reference entity name.
     *
     * @return string
     */
    public function getRefEntityName(): ?string
    {
        return $this->refEntityName;
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function setRef(EntityInterface $entity = null): static
    {
        $this->setRefId($entity ? $entity->getId() : null);
        $this->setRefEntityName($entity ? ClassLookup::getClass($entity) : null);

        return $this;
    }

    /**
     * @return AbstractEntity
     */
    public function getRef(EntityManager $em): mixed
    {
        return $em->getRepository($this->getRefEntityName())->find($this->getRefId());
    }

    /**
     * @return string
     */
    public function getDefaultAdminType(): string
    {
        return SeoType::class;
    }

    /**
     * Set refId.
     *
     * @param int $refId
     *
     * @return Seo
     */
    protected function setRefId(?int $refId): self
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Set reference entity name.
     *
     * @param string $refEntityName
     *
     * @return Seo
     */
    protected function setRefEntityName(?string $refEntityName): self
    {
        $this->refEntityName = $refEntityName;

        return $this;
    }
}

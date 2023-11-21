<?php

namespace Hgabka\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\SeoBundle\Form\RobotsType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_seo_robots')]
class Robots
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'robots_txt', type: 'text', nullable: true)]
    protected ?string $robotsTxt = null;

    /**
     * Return string representation of entity.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'hg_seo.admin.label.robots';
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id The unique identifier
     *
     * @return AbstractEntity
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getRobotsTxt(): ?string
    {
        return $this->robotsTxt;
    }

    /**
     * @param string $robotsTxt
     */
    public function setRobotsTxt(?string $robotsTxt): self
    {
        $this->robotsTxt = $robotsTxt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultAdminType(): string
    {
        return RobotsType::class;
    }
}

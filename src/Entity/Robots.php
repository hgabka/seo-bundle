<?php

namespace Hgabka\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\SeoBundle\Form\RobotsType;

/**
 * Robots.txt data.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_seo_robots")
 */
class Robots
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="robots_txt", type="text", nullable=true)
     */
    protected $robotsTxt;

    /**
     * Return string representation of entity.
     *
     * @return string
     */
    public function __toString()
    {
        return 'Robots';
    }

    /**
     * @return string
     */
    public function getRobotsTxt()
    {
        return $this->robotsTxt;
    }

    /**
     * @param string $robotsTxt
     */
    public function setRobotsTxt($robotsTxt)
    {
        $this->robotsTxt = $robotsTxt;
    }

    /**
     * @return string
     */
    public function getDefaultAdminType()
    {
        return RobotsType::class;
    }
}

<?php

namespace Hgabka\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\UtilsBundle\Helper\ClassLookup;

/**
 * Repository for Seo.
 */
class SeoRepository extends EntityRepository
{
    /**
     * Find the seo information for the given entity.
     *
     * @param $entity
     *
     * @return Seo
     */
    public function findFor($entity)
    {
        return $this->findOneBy(['refId' => $entity->getId(), 'refEntityName' => ClassLookup::getClass($entity)]);
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function findOrCreateFor($entity)
    {
        $seo = $this->findFor($entity);

        if (null === $seo) {
            $seo = new Seo();
            $seo->setRef($entity);
        }

        return $seo;
    }

    public function findGeneral()
    {
        return
            $this
                ->createQueryBuilder('s')
                ->where('s.refId IS NULL')
                ->andWhere('s.refEntityName IS NULL')
                ->orderBy('s.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }
}

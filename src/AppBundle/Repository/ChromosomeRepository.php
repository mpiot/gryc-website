<?php

namespace AppBundle\Repository;

/**
 * ChromosomeRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChromosomeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getChromosomeWithStrainAndSpecies($name) {
        $query = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species')
            ->where('c.name = :name')
                ->setParameter('name', $name)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}

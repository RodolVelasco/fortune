<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ParticipaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticipaRepository extends EntityRepository
{
    /* advanced search */
    public function search($searchParam) {
        extract($searchParam);
        $qb = $this->createQueryBuilder('p')
                   ->leftJoin('p.sorteo','s')
                   ->addSelect('s')
                   ->leftJoin('p.user','u')
                   ->addSelect('u')
                   ;

        if(!empty($ids))
            $qb->andWhere('p.id in (:ids) AND p.user is null')->setParameter('ids', $ids);


        if(!empty($sortBy)){
            $sortBy = in_array($sortBy, array('id')) ? $sortBy : 'id';
            $sortDir = ($sortDir == 'ASC') ? 'ASC' : 'DESC';
            $qb->orderBy('p.' . $sortBy, $sortDir);
        }

        if(!empty($perPage)) $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

       return new Paginator($qb->getQuery());
    }

    public function counter() {
        $qb = $this->createQueryBuilder('p')->select('COUNT(p)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function verificadorSoyElUltimo($id) {
        $qb = $this->createQueryBuilder('p')->select('COUNT(p)')->where('p.sorteo = '.$id.' and p.user is NULL');
        return $qb->getQuery()->getSingleScalarResult();
    }

    private function fetch($query)
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute();
        return  $stmt->fetchAll();
    }
}

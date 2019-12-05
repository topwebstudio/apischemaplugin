<?php

namespace App\Repository;

trait BaseRepository {

    public $qb;

    

    public function findAll() {
        $this->getQb();

        $this->qb->orderBy('obj.id', 'DESC');
        return $this->getResult();
    }

    public function getQb() {
        $repository = $this->getEntityManager()->getRepository('App:' . $this->entity);
        $this->qb = $repository->createQueryBuilder('obj');
    }

    public function getResult() {
        $this->query = $this->qb->getQuery();

        return $this->query->getResult();
    }

    public function getSingleResult() {
        $this->qb->setMaxResults(1);
        $this->query = $this->qb->getQuery();

        try {
            $result = $this->query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {

            $result = null;
        }

        return $result;
    }

}

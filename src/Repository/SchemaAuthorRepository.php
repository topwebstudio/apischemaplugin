<?php

namespace App\Repository;

class SchemaAuthorRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'SchemaAuthor';

    public function getUserByNameEmailAndDifferentUid($uid, $name, $email) {
        $this->getQb();

        $this->qb->where("obj.uid != :uid");

        $this->qb->andWhere($this->qb->expr()->orX(
                        'obj.name = :name', 'obj.email = :email'
        ));

        $this->qb->setParameter('uid', $uid);
        $this->qb->setParameter('name', $name);
        $this->qb->setParameter('email', $email);

        return $this->getSingleResult();
    }

}

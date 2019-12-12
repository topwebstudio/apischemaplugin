<?php

namespace App\Repository;

class SchemaAuthorRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'SchemaAuthor';

    public function getUserByNicknameEmailAndDifferentUid($uid, $nickname, $email) {
        $this->getQb();

        $this->qb->where("obj.uid != :uid");

        $this->qb->andWhere($this->qb->expr()->orX(
                        'obj.nickname = :nickname', 'obj.email = :email'
        ));

        $this->qb->setParameter('uid', $uid);
        $this->qb->setParameter('nickname', $nickname);
        $this->qb->setParameter('email', $email);

        return $this->getSingleResult();
    }

}

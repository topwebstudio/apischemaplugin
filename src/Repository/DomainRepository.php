<?php

namespace App\Repository;

class DomainRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'Domain';

    public function findAll($key = null) {
        $this->getQb();

        if ($key) {
            $this->qb->where("obj.uid = :uid")->setParameter('uid', $key);
        }

        $this->qb->orderBy('obj.id', 'DESC');
        return $this->getResult();
    }

    public function findOneByApiKeyAndDomainUrl($apiKey, $domain) {
        $this->getQb();

        $this->qb->leftJoin('obj.purchase', 'p');
        $this->qb->where('obj.domain = :domain')->setParameter('domain', $domain);
        $this->qb->andWhere('
                    p.licenses
                    LIKE :key')->setParameter('key', '%' . $apiKey . '%');

        return $this->getSingleResult();
    }

    public function findOtherApiKeyDomain($apiKey, $domain) {
        $this->getQb();

        $this->qb->leftJoin('obj.purchase', 'p');
        $this->qb->where('obj.domain = :domain')->setParameter('domain', $domain);
        $this->qb->andWhere('p.licenses NOT LIKE :key')->setParameter('key', '%' . $apiKey . '%');

        return $this->getSingleResult();
    }

}

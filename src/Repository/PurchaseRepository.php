<?php

namespace App\Repository;

class PurchaseRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'Purchase';

    public function search($search) {
        $this->getQb();

        if ($search) {
            $this->qb->andWhere('
                    obj.transactionId
                    LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        $this->qb->setMaxResults(10);

        $this->qb->orderBy('obj.id', 'DESC');
        return $this->getResult();
    }

    public function getActivePurchaseByApiKey($key) {
        $this->getQb();

        $this->qb->leftJoin('obj.licenses', 'l');
        $this->qb->where('l.licenseKey = :key')->setParameter('key', $key);


        $this->qb->andWhere("obj.enabled = 1");
        $this->qb->andWhere("obj.isActivePurchase = 1");

        return $this->getSingleResult();
    }

}

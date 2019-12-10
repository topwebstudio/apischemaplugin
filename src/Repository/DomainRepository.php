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

        $this->qb->leftJoin('obj.license', 'l');

        $this->qb->where('obj.domain = :domain')->setParameter('domain', $domain);
        $this->qb->andWhere('l.licenseKey = :licenseKey')->setParameter('licenseKey', $apiKey);

        return $this->getSingleResult();
    }

    public function findLicensedDomainsCount($key, $domainToExclude) {
        $repository = $this->getEntityManager()->getRepository('App:Domain');
        $this->qb = $repository->createQueryBuilder('obj')->select('COUNT(obj.id) as counter');

        $this->qb->leftJoin('obj.license', 'l');

        $this->qb->where('obj.domain != :domain')->setParameter('domain', $domainToExclude);
        $this->qb->andWhere("l.licenseKey = :key")->setParameter('key', $key);

        $query = $this->qb->getQuery();

        try {
            $result = $query->getSingleScalarResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $result = 0;
        }

        return $result;
    }

    public function findOtherApiKeyDomain($apiKey, $domain) {
        $this->getQb();

        $this->qb->leftJoin('obj.license', 'l');

        $this->qb->where('obj.domain = :domain')->setParameter('domain', $domain);
        $this->qb->andWhere('l.licenseKey != :key')->setParameter('key', $apiKey);

        return $this->getSingleResult();
    }

}

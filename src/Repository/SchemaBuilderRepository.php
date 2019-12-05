<?php

namespace App\Repository;

class SchemaBuilderRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'SchemaBuilder';

    public function fetch($parameters = array()) {
        $repository = $this->getEntityManager()->getRepository('App:SchemaBuilder');
        $qb = $repository->createQueryBuilder('obj');

        $qb->leftJoin('obj.jsonSchemas', 'sch');
        $qb->leftJoin('obj.author', 'auth');

        if (isset($parameters['uid']) && $parameters['uid']) {
            $qb->where($qb->expr()->orX('obj.published = 1', 'auth.uid = :uid'));
            $qb->setParameter('uid', $parameters['uid']);
        } else {
            $qb->where('obj.published = 1');
        }

        if (isset($parameters['filterByAuthor']) && $parameters['filterByAuthor']) {
            $qb->andWhere('auth.id = :filterByAuthor');
            $qb->setParameter('filterByAuthor', $parameters['filterByAuthor']);
        }

        if (isset($parameters['search']) && $parameters['search']) {
            $search = $parameters['search'];
            $qb->andWhere($qb->expr()->orX(
                    'obj.title LIKE :search', 
                    'obj.content LIKE :search', 
                    'auth.name LIKE :search',
                    'sch.schemaArray LIKE :search'
                    ));
            $qb->setParameter('search', '%' . $search . '%');
        }


        $qb->andWhere('obj.jsonSchemas IS NOT EMPTY');
        $qb->orderBy('obj.featured', 'DESC');
        $qb->addOrderBy('obj.id', 'DESC');


        $query = $qb->getQuery();

        return $query->getResult();
    }

}

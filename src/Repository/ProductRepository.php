<?php

namespace App\Repository;

class ProductRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'Product';

   

}

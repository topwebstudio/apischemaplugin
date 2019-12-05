<?php

namespace App\Repository;

class JsonSchemaRepository extends \Doctrine\ORM\EntityRepository {

    use BaseRepository;

    public $entity = 'JsonSchema';
 
}

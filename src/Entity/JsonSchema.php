<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="json_schemas")
 * @ORM\Entity(repositoryClass="App\Repository\JsonSchemaRepository")
 */
class JsonSchema {

    use EntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="SchemaBuilder", inversedBy="jsonSchemas")
     */
    private $schema_;

    /**
     * @var string
     *
     * @ORM\Column(name="schema_array", type="array")
     */
    private $schemaArray;

    public function setSchemaArray($schemaArray) {
        $this->schemaArray = $schemaArray;
    }

    public function getSchemaArray() {
        return $this->strip_slashes_recursive($this->schemaArray);
    }

    function strip_slashes_recursive($variable) {
        if (is_string($variable))
            return stripslashes($variable);
        if (is_array($variable))
            foreach ($variable as $i => $value)
                $variable[$i] = $this->strip_slashes_recursive($value);

        return $variable;
    }

    public function getSchema() {
        return $this->schema_;
    }

    public function setSchema($schema) {
        $this->schema_ = $schema;
    }

    public function __toString() {
        return 'I CAUSE THIS ISSUE';
    }

}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SchemaAuthor
 *
 * @ORM\Table(name="schema_authors")
 * @ORM\Entity(repositoryClass="App\Repository\SchemaAuthorRepository")
 */
class SchemaAuthor {

    use EntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string attribute
     *
     * @ORM\Column(name="uid", type="string", length=100, unique=true, nullable=false)
     */
    private $uid;

    /**
     * @var string attribute
     * @Assert\NotBlank(message = "Name should not be blank")
     * @ORM\Column(name="title", type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     * @var string attribute
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     * @Assert\NotBlank(message = "Name should not be blank")
     * @Assert\Email(
     *     message = "The email provided is not a valid email.",
     *     checkMX = true
     * )
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=false)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(message = "Name should not be blank")
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="SchemaBuilder", mappedBy="author")
     */
    protected $schemaBuilders;

    public function getSchemaBuilders(){
        return $this->schemaBuilders;
    }
    
    
    public function addSchemaBuilder($schemaBuilder) {
        if (!$this->schemaBuilders->contains($schemaBuilder)) {
            $this->schemaBuilders[] = $schemaBuilder;
            $schemaBuilder->setAuthor($this);
        }
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getUid() {
        return $this->uid;
    }

}

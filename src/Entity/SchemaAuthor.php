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
     * @Assert\NotBlank(message = "Nickname should not be blank")
     * @ORM\Column(name="nickname", type="string", length=255, unique=true, nullable=false)
     */
    private $nickname;

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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    public function getSchemaBuilders() {
        return $this->schemaBuilders;
    }

    public function addSchemaBuilder($schemaBuilder) {
        if (!$this->schemaBuilders->contains($schemaBuilder)) {
            $this->schemaBuilders[] = $schemaBuilder;
            $schemaBuilder->setAuthor($this);
        }
    }

    public function setNickname($nickname) {
        $this->nickname = $nickname;
    }

    public function getNickname() {
        return $this->nickname;
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

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): self {
        $this->name = $name;

        return $this;
    }

    public function setContent($content) {
        $this->content = strip_tags($content);

        return $this;
    }

}

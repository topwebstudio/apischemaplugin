<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SchemaBuilder
 *
 * @ORM\Table(name="schema_builders")
 * @ORM\Entity(repositoryClass="App\Repository\SchemaBuilderRepository")
 */
class SchemaBuilder {

    use EntityTrait;

    public function __construct() {
        $this->jsonSchemas = new ArrayCollection();
    }

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
     * @Assert\NotBlank(message = "Schema Title should not be blank")
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var bool
     *
     * @ORM\Column(name="featured", type="boolean", nullable=true)
     */
    private $featured;

    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var string
     * @Gedmo\Slug(fields={"title"}) 
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="JsonSchema", mappedBy="schema_", orphanRemoval=true, cascade={"persist"})
     */
    private $jsonSchemas;

    /**
     * @ORM\ManyToOne(targetEntity="SchemaAuthor", inversedBy="schemaBuilders")
     * @var SchemaAuthor
     */
    protected $author;
    protected $authorEmail;

    public function getAuthorEmail() {
        return $this->authorEmail;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return SchemaBuilder
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return SchemaBuilder
     */
    public function setTags($tags) {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return SchemaBuilder
     */
    public function setPublished($published) {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return bool
     */
    public function getPublished() {
        return $this->published;
    }

    /**
     * Set featured
     *
     * @param boolean $featured
     *
     * @return SchemaBuilder
     */
    public function setFeatured($featured) {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return bool
     */
    public function getFeatured() {
        return $this->featured;
    }

    /**
     * Set datePublished
     *
     * @param \DateTime $datePublished
     *
     * @return SchemaBuilder
     */
    public function setDatePublished($datePublished) {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Get datePublished
     *
     * @return \DateTime
     */
    public function getDatePublished() {
        return $this->datePublished;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    //

    public function setJson($json) {

        $this->json = $json;
    }

    public function getJson() {
        return $this->json;
    }

    public function getJsonSchemas() {
        return $this->jsonSchemas;
    }

    public function getJsonSchemasArray() {
        $return = [];

        foreach ($this->jsonSchemas as $schema) {
            $return[] = $schema->getSchemaArray();
        }

        return $return;
    }

    public function addJsonSchema($jsonSchema) {
        if (!$this->jsonSchemas->contains($jsonSchema)) {
            $this->jsonSchemas[] = $jsonSchema;
            $jsonSchema->setSchema($this);
        }

        return $this;
    }

    public function removeJsonSchema($jsonSchema) {
        if ($this->jsonSchemas->contains($jsonSchema)) {
            $this->jsonSchemas->removeElement($jsonSchema);

            if ($jsonSchema->getSchema() === $this) {
                $jsonSchema->setSchema(null);
            }
        }

        return $this;
    }

    public function setAuthor($author) {
        $this->author = $author;
        $author->addSchemaBuilder($this);
    }

    public function getAuthor() {
        return $this->author;
    }

    /**
     * @ORM\ManyToOne(targetEntity="PluginVersion")
     */
    private $pluginVersion;

    public function getPluginVersion() {
        return $this->pluginVersion;
    }

    public function setPluginVersion($pluginVersion) {
        $this->pluginVersion = $pluginVersion;
    }

}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PluginVersion
 *
 * @ORM\Table(name="plugin_versions")
 * @ORM\Entity(repositoryClass="App\Repository\PluginVersionRepository")
 */
class PluginVersion {

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
     * @var string
     *
     * @ORM\Column(name="version", type="float", scale=2)
     */
    private $version = '1.0';

    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="tested_version", type="float", scale=2)
     */
    private $testedVersion = '5.3';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="installation", type="text", nullable=true)
     */
    private $installation;

    /**
     * @var string
     *
     * @ORM\Column(name="changelog", type="text", nullable=true)
     */
    private $changelog;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $archive;

    public function getArchive() {
        return $this->archive;
    }

    public function setArchive($archive) {
        if ($archive) {
            $this->archive = $archive;
        }


        return $this;
    }

    public function setDownloadUrl($downloadUrl) {
        $this->downloadUrl = $downloadUrl;
    }

    public function getDownloadUrl() {
        return $this->downloadUrl;
    }

    public function getTestedVersion() {
        return $this->testedVersion;
    }

    public function setTestedVersion($testedVersion) {
        $this->testedVersion = $testedVersion;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setInstallation($installation) {
        $this->installation = $installation;
    }

    public function getInstallation() {
        return $this->installation;
    }

    public function getChangelog() {
        return $this->changelog;
    }

    public function setChangelog($changelog) {
        $this->changelog = $changelog;
    }

}

<?php

namespace App\Entity;

trait EntityTrait {

   public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }
    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?\DateTimeInterface $dateUpdated): self {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }
    
        public function getEnabled(): ?bool {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Country
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Country
     */
    public function setSlug($slug) {
        $this->slug = strtoupper($slug);

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }
 

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Location
     */
    public function setZip($zip) {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Location
     */
    public function setLat($lat) {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon
     *
     * @return Location
     */
    public function setLon($lon) {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return string
     */
    public function getLon() {
        return $this->lon;
    }

    public function getCounty() {
        return $this->county;
    }

    public function setCounty($county) {
        $this->county = $county;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function getLocationsCount() {
        return $this->locationsCount;
    }

    public function setLocationsCount($locationsCount) {
        $this->locationsCount = $locationsCount;
    }

    public function getVersion() {
        if (strpos($this->version, '.') === false) {
            return $this->version . '.0';
        }
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = number_format((float) $version, 2, '.', '');
        
        return $this;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setUid($uid) {
        $this->uid = $uid;
    }

   

    function fixIndiaCharacters($str) {
        $str = str_replace('ā', 'a', $str);
        $str = str_replace('ū', 'u', $str);
        $str = str_replace('ī', 'i', $str);
        $str = str_replace('ō', 'o', $str);
        $str = str_replace('ē', 'e', $str);


        $str = str_replace('Ā', 'A', $str);
        $str = str_replace('Ū', 'U', $str);
        $str = str_replace('Ī', 'I', $str);
        $str = str_replace('Ō', 'O', $str);
        $str = str_replace('Ē', 'E', $str);



        return $str;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

}

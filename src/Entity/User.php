<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser {

    use EntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank(message="This field is required")
     * @Assert\Length(
     *      min = "3",
     *      max = "50",
     *      minMessage = "[-Inf,Inf]Your username is too short",
     *      maxMessage = "[-Inf,Inf]The lenght of your name should be less than {{ limit }} characters"
     * )
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="chosen_country", type="integer", nullable=true)
     */
    private $country;

    /**
     * @var datetime $dateRegistered
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_registered", type="datetime")
     */
    protected $dateRegistered;

    /**
     * @var datetime $dateModified
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_modified", type="datetime")
     */
    protected $dateModified;

    public function getDateRegistered() {
        return $this->dateRegistered;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function setEnabled($enabled)  {
        $this->enabled =$enabled;
    }

}

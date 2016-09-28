<?php

namespace Pouce\UserBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * School
 * 
 * @ORM\Table(name="school")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Pouce\UserBundle\Entity\SchoolRepository")
 */
class School
{
    /**
     * School id
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"school","user"})
     *
     * @var integer
     */
    private $id;

    /**
     * School name
     * 
     * @ORM\Column(name="name", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"school","user"})
     *
     * @var string
     */
    private $name;

    /**
     * School initials
     * 
     * @ORM\Column(name="sigle", type="string", length=10, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Groups({"school","user"})
     *
     * @var string
     */
    private $sigle;

    /**
     * School address
     * 
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     * @Groups({"school"})
     *
     * @var string
     */
    private $address;

    /**
     * School telephone
     * 
     * @ORM\Column(name="telephone", type="string", length=20, nullable=true)
     *
     * @Groups({"school"})
     *
     * @var string
     */
    private $telephone;

    /**
     * City where the school is based
     * 
     * @ORM\ManyToOne(targetEntity="Pouce\SiteBundle\Entity\City", cascade={"persist"})
     *
     * @Groups({"school","user"})
    */
    private $city;

    /**
     * Editions in which the school participate
     * 
     * @ORM\ManyToMany(targetEntity="Pouce\SiteBundle\Entity\Edition", inversedBy="schools")
     */
    private $editions;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return School
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     * @return School
     */
    public function setSigle($sigle)
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string 
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * Set autoriseInscription
     *
     * @param boolean $autoriseInscription
     * @return School
     */
    public function setAutoriseInscription($autoriseInscription)
    {
        $this->autoriseInscription = $autoriseInscription;

        return $this;
    }

    /**
     * Get autoriseInscription
     *
     * @return boolean 
     */
    public function getAutoriseInscription()
    {
        return $this->autoriseInscription;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->editions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add editions
     *
     * @param \Pouce\SiteBundle\Entity\Edition $editions
     * @return School
     */
    public function addEdition(\Pouce\SiteBundle\Entity\Edition $editions)
    {
        $this->editions[] = $editions;

        return $this;
    }

    /**
     * Remove editions
     *
     * @param \Pouce\SiteBundle\Entity\Edition $editions
     */
    public function removeEdition(\Pouce\SiteBundle\Entity\Edition $editions)
    {
        $this->editions->removeElement($editions);
    }

    /**
     * Get editions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEditions()
    {
        return $this->editions;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return School
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }


    /**
     * Set telephone
     *
     * @param string $telephone
     * @return School
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set city
     *
     * @param \Pouce\SiteBundle\Entity\City $city
     * @return School
     */
    public function setCity(\Pouce\SiteBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Pouce\SiteBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}

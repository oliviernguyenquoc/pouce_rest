<?php

namespace Pouce\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="Pouce\SiteBundle\Entity\CityRepository")
 */
class City
{
    /**
     * Id of the city
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"city","position","team","result","user","school"})
     *
     * @var integer
    */
    private $id;

    /**
     * City name
     * 
     * @ORM\Column(name="name", type="string", length=35)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"city","position","team","result","user","school"})
     *
     * @var string
     */
    private $name;

    /**
     * Country
     * 
     * @ORM\ManyToOne(targetEntity="Pouce\SiteBundle\Entity\Country", cascade={"persist"})
     * 
     * @Groups({"city","position","team","result"})
     */
    private $country;

    /**
     * Province
     * 
     * @ORM\Column(name="province", type="string", length=35, nullable=true)
     *
     * @var string
     */
    private $province;

    /**
     * Number of people in the city
     *
     * @ORM\Column(name="population", type="integer", nullable=true)
     * 
     * @Assert\Range(min=0)
     *
     * @Groups({"city"})
     *
     * @var integer
     */
    private $population;

    /**
     * City longitude
     * 
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * 
     * @Assert\Range(min=-180)
     * @Assert\Range(max=180)
     *
     * @Groups({"city","position","team"})
     * 
     * @var float
     */
    private $longitude;

    /**
     * City latitude
     * 
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * 
     * @Assert\Range(min=-90)
     * @Assert\Range(max=90)
     *
     * @Groups({"city","position","team"})
     *
     * @var float
     */
    private $latitude;


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
     * @return City
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
     * Set province
     *
     * @param string $province
     * @return City
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set population
     *
     * @param integer $population
     * @return City
     */
    public function setPopulation($population)
    {
        $this->population = $population;

        return $this;
    }

    /**
     * Get population
     *
     * @return integer 
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return City
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return City
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }



    /**
     * Set country
     *
     * @param \Pouce\SiteBundle\Entity\Country $country
     * @return City
     */
    public function setCountry(\Pouce\SiteBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Pouce\SiteBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}

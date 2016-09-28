<?php

namespace Pouce\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Pouce\SiteBundle\Entity\CountryRepository")
 */
class Country
{
    /**
     * Country id
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"city"})
     *
     * @var integer
     */
    private $id;

    /**
     * Country name
     *
     * @ORM\Column(name="name", type="string", length=35)
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"position","city","result"})
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="code", type="string", length=4)
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Country
     *
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(name="capital", type="string", length=35, nullable=true)
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @var string 
     */
    private $capital;

    /**
     * @ORM\Column(name="province", type="string", length=35, nullable=true)
     *
     * @var string 
     */
    private $province;

    /**
     * @ORM\Column(name="area", type="float", nullable=true)
     * 
     * @Assert\Range(min=0)
     *
     * @var float
     */
    private $area;

    /**
     * @ORM\Column(name="population", type="integer", nullable=true)
     * 
     * @Assert\Range(min=0)
     *
     * @var integer
     */
    private $population;


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
     * @return Country
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
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set capital
     *
     * @param string $capital
     * @return Country
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get capital
     *
     * @return string 
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * Set area
     *
     * @param float $area
     * @return Country
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return float 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set population
     *
     * @param integer $population
     * @return Country
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
     * Set province
     *
     * @param string $province
     * @return Country
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
}

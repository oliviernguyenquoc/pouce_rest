<?php

namespace Pouce\TeamBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Position
 *
 * @ORM\Table(name="position")
 * @ORM\Entity(repositoryClass="Pouce\TeamBundle\Entity\PositionRepository")
 */
class Position
{
    /**
     * Position id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Groups({"position","team"})
     *
     * @var integer
     */
    private $id;

    /**
     * City of the position
     * 
     * @ORM\ManyToOne(targetEntity="Pouce\SiteBundle\Entity\City")
     * @ORM\JoinColumn(nullable=true)
     * 
     * @Groups({"position","team"})
    */
    private $city;

    /**
     * Distance between departure of the team and the position
     *
     * @ORM\Column(name="distance", type="float", nullable=true)
     *
     * @Groups({"position","team"})
     *
     * @var float
     */
    private $distance;

    /**
     * Team who own the position
     * 
     * @ORM\ManyToOne(targetEntity="Pouce\TeamBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
    */
    private $team;

    /**
     * Datetime when the position have been registered
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     *
     * @Groups({"position"})
     *
     * @var datetime $created
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime $updated
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
     * Set distance
     *
     * @param float $distance
     * @return Position
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }


    /**
     * Set team
     *
     * @param \Pouce\TeamBundle\Entity\Team $team
     * @return Position
     */
    public function setTeam(\Pouce\TeamBundle\Entity\Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \Pouce\TeamBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Position
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Position
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Position
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
}

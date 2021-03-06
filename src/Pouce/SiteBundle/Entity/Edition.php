<?php

namespace Pouce\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * Edition
 *
 * @ORM\Table(name="edition")
 * @ORM\Entity(repositoryClass="Pouce\SiteBundle\Entity\EditionRepository")
 */
class Edition
{
    /**
     * Edition id
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"edition","team"})
     *
     * @var integer
     */
    private $id;

    /**
     * Schools which participate to edition
     * 
     * @ORM\ManyToMany(targetEntity="Pouce\UserBundle\Entity\School", mappedBy="editions")
     */
    private $schools;

    /**
     * First day of the edition
     * 
     * @ORM\Column(name="dateOfEvent", type="date")
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"edition","team"})
     *
     * @var date
     */
    private $dateOfEvent;

    /**
     * Edition status (registering, finished, scheduled or inProgress)
     * 
     * @ORM\Column(name="status", type="string", length=35)
     * 
     * @Assert\Choice({"registering", "finished", "scheduled", "inProgress"})
     *
     * @Groups({"edition","team"})
     *
     * @var string
     */
    private $status;

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
     * Constructor
     */
    public function __construct()
    {
        $this->schools = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add schools
     *
     * @param \Pouce\UserBundle\Entity\School $schools
     * @return Edition
     */
    public function addSchool(\Pouce\UserBundle\Entity\School $schools)
    {
        $this->schools[] = $schools;
        $schools->setEdition($this); //Ajout de la génération pour ne pas boucler à l'infinie (SdZ p.215)
        return $this;
    }
    /**
     * Remove schools
     *
     * @param \Pouce\UserBundle\Entity\School $schools
     */
    public function removeSchool(\Pouce\UserBundle\Entity\School $schools)
    {
        $this->schools->removeElement($schools);
        $school->setEdition(null);
    }
    
    /**
     * Get schools
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Edition
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateOfEvent
     *
     * @param \DateTime $dateOfEvent
     * @return Edition
     */
    public function setDateOfEvent($dateOfEvent)
    {
        $this->dateOfEvent = $dateOfEvent;

        return $this;
    }

    /**
     * Get dateOfEvent
     *
     * @return \DateTime 
     */
    public function getDateOfEvent()
    {
        return $this->dateOfEvent;
    }

}

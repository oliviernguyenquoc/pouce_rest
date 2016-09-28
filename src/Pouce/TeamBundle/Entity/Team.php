<?php

namespace Pouce\TeamBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="Pouce\TeamBundle\Entity\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"team"})
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Pouce\UserBundle\Entity\User", inversedBy="teams")
     *
     * @Groups({"team"})
    */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="Pouce\SiteBundle\Entity\Edition", cascade={"persist"})
     *
     * @Groups({"team"})
    */
    private $edition;

    /**
     * @ORM\OneToOne(targetEntity="Pouce\TeamBundle\Entity\Result", mappedBy="team", cascade={"remove"})
    */
    private $result;

    /**
     * @ORM\ManyToOne(targetEntity="Pouce\SiteBundle\Entity\City", cascade={"persist"})
     *
     * @Groups({"team"})
    */
    private $startCity;

    /**
     * @ORM\Column(name="teamName", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"team"})
     *
     * @var string
     */
    private $teamName;

    /**
     * @ORM\Column(name="comment", type="string", length=500)
     *
     * @Groups({"team"})
     *
     * @var string
     */
    private $comment;

    /**
     * @ORM\Column(name="targetDestination", type="string", length=255)
     * 
     * @Assert\NotBlank()
     *
     * @Groups({"team"})
     *
     * @var string
     */
    private $targetDestination;

    /**
     * @ORM\Column(name="finishRegister", type="boolean")
     *
     * @var boolean
     */
    private $finishRegister;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     * 
     * @var datetime $created
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     * 
     * @var \DateTime $updated
     */
    private $updated;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(\Pouce\UserBundle\Entity\User $user)
    {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau
        $this->users[] = $user;

        return $this;
    }

    public function removeUser(\Pouce\UserBundle\Entity\User $user)
    {
        $this->categories->removeElement($user);
    }

    public function setResult(Result $result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set users
     * @param \Doctrine\Common\Collections\Collection $users
     *
     * @return Post
     */
    public function setUsers($users)
    {

        if(!is_array($users))
        {
            $users = array($users);
        }
        $this->users = $users;

        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }


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
     * Set teamName
     *
     * @param string $teamName
     * @return Team
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;

        return $this;
    }

    /**
     * Get teamName
     *
     * @return string 
     */
    public function getTeamName()
    {
        return $this->teamName;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Team
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set targetDestination
     *
     * @param string $targetDestination
     * @return Team
     */
    public function setTargetDestination($targetDestination)
    {
        $this->targetDestination = $targetDestination;

        return $this;
    }

    /**
     * Get targetDestination
     *
     * @return string 
     */
    public function getTargetDestination()
    {
        return $this->targetDestination;
    }

    /**
     * Set finishRegister
     *
     * @param boolean $finishRegister
     * @return Team
     */
    public function setFinishRegister($finishRegister)
    {
        $this->finishRegister = $finishRegister;

        return $this;
    }

    /**
     * Get finishRegister
     *
     * @return boolean 
     */
    public function getFinishRegister()
    {
        return $this->finishRegister;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Team
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
     * @return Team
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
     * Set edition
     *
     * @param \Pouce\SiteBundle\Entity\Edition $edition
     * @return Team
     */
    public function setEdition(\Pouce\SiteBundle\Entity\Edition $edition = null)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return \Pouce\SiteBundle\Entity\Edition 
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set startCity
     *
     * @param \Pouce\SiteBundle\Entity\City $startCity
     * @return Team
     */
    public function setStartCity(\Pouce\SiteBundle\Entity\City $startCity = null)
    {
        $this->startCity = $startCity;

        return $this;
    }

    /**
     * Get startCity
     *
     * @return \Pouce\SiteBundle\Entity\City  
     */
    public function getStartCity()
    {
        return $this->startCity;
    }
}

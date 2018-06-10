<?php

namespace EspritEntreAide\EvenementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EspritEntreAide\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass="EspritEntreAide\EvenementBundle\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EspritEntreAide\UserBundle\Entity\User", inversedBy="evenements")
     * @ORM\JoinColumn(name="id_user",referencedColumnName="id")
     */
    private $idUser;



    /**
     * @var string
     *
     * @ORM\Column(name="titre_e", type="string", length=255, nullable=true)
     */
    private $titreE;


    /**
     * @var string
     *
     * @ORM\Column(name="desc_e", type="text", nullable=true)
     */
    private $descE;

    /**
     * @var \DateTime
     * @Assert\GreaterThan("today")
     * @ORM\Column(name="date_e", type="datetime", nullable=true)
     */
    private $dateE;



    /**
     * @ORM\ManyToOne(targetEntity="EspritEntreAide\ClubBundle\Entity\Club", inversedBy="evenements")
     * @ORM\JoinColumn(name="id_club",referencedColumnName="id")
     */
    private $idClub;

    /**
     * @return mixed
     */
    public function getIdClub()
    {
        return $this->idClub;
    }

    /**
     * @param mixed $idClub
     */
    public function setIdClub($idClub)
    {
        $this->idClub = $idClub;
    }




    /**
     * @var string
     *
     * @ORM\Column(name="usr_role", type="string", nullable=true)
     */
    private $usrRole;

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param mixed $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return string
     */
    public function getUsrRole()
    {
        return $this->usrRole;
    }

    /**
     * @param string $usrRole
     */
    public function setUsrRole($usrRole)
    {
        $this->usrRole = $usrRole;
    }






    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Ajouter une image jpg")
     * @Assert\File(mimeTypes={ "image/jpeg" })
     */
    private $image;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="type_e", type="string", length=255, nullable=true)
     */
    private $typeE;

    /**
     * @var int
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat=0;

    /**
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param int $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }




    /**
     *
     * @ORM\Column(name="googlemaps", type="array", length=255, nullable=true)
     */
    private $Googlemaps;

    /**
     * @return mixed
     */
    public function getGooglemaps()
    {
        return $this->Googlemaps;
    }

    /**
     * @param mixed $Googlemaps
     */
    public function setGooglemaps($Googlemaps)
    {
        $this->Googlemaps = $Googlemaps;
    }




    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modif", type="date", nullable=true)
     */
    private $dateModif;

    /**
     * @ORM\ManyToMany(targetEntity="EspritEntreAide\UserBundle\Entity\User", inversedBy="participations")
     * @ORM\JoinTable(name="participants_evenements")
     */
    private $participants;

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    public function __construct() {
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param mixed $participants
     */
    public function addParticipants($participants)
    {
        $this->participants->add($participants);
    }
    /**
     * @param mixed $participants
     */
    public function removeParticipants($participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * @return bool
     */
    public function checkParticipant($participant){
       return $this->participants->contains($participant);
    }

    /**
     * @return int
     */
    public function countParticipant(){
        return $this->participants->count();
    }



    /**
     * Set descE
     *
     * @param string $descE
     *
     * @return Evenement
     */
    public function setDescE($descE)
    {
        $this->descE = $descE;

        return $this;
    }

    /**
     * Get descE
     *
     * @return string
     */
    public function getDescE()
    {
        return $this->descE;
    }

    /**
     * Set dateE
     *
     * @param \DateTime $dateE
     *
     * @return Evenement
     */
    public function setDateE($dateE)
    {
        $this->dateE = $dateE;

        return $this;
    }

    /**
     * Get dateE
     *
     * @return \DateTime
     */
    public function getDateE()
    {
        return $this->dateE;
    }




    /**
     * Set typeE
     *
     * @param string $typeE
     *
     * @return Evenement
     */
    public function setTypeE($typeE)
    {
        $this->typeE = $typeE;

        return $this;
    }

    /**
     * Get typeE
     *
     * @return string
     */
    public function getTypeE()
    {
        return $this->typeE;
    }

    /**
     * Set dateModif
     *
     * @param \DateTime $dateModif
     *
     * @return Evenement
     */
    public function setDateModif($dateModif)
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    /**
     * Get dateModif
     *
     * @return \DateTime
     */
    public function getDateModif()
    {
        return $this->dateModif;
    }
    /**
     * Set titreE
     *
     * @param string $titreE
     *
     * @return Evenement
     */
    public function setTitreE($titreE)
    {
        $this->titreE = $titreE;

        return $this;
    }

    /**
     * Get titreE
     *
     * @return string
     */
    public function getTitreE()
    {
        return $this->titreE;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }


}


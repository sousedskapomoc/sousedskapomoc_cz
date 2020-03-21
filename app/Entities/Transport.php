<?php


namespace SousedskaPomoc\Entities;

use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Volunteer;


/**
 * Class Transport
 * @ORM\Entity
 */
class Transport
{
    use Id;

//    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $sorting;

    /**
     * @ORM\OneToMany(targetEntity="Volunteer", mappedBy="transport")
     */
    protected $volunteers;



    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }



    /**
     * @param mixed $type
     */
    public function setType($type) : void
    {
        $this->type = $type;
    }



    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }



    /**
     * @param mixed $active
     */
    public function setActive($active) : void
    {
        $this->active = $active;
    }



    /**
     * @return mixed
     */
    public function getSorting()
    {
        return $this->sorting;
    }



    /**
     * @param mixed $sorting
     */
    public function setSorting($sorting) : void
    {
        $this->sorting = $sorting;
    }



    /**
     * @return mixed
     */
    public function getVolunteers()
    {
        return $this->volunteers;
    }



    /**
     * @param mixed $volunteers
     */
    public function setVolunteers($volunteers) : void
    {
        $this->volunteers = $volunteers;
    }
}
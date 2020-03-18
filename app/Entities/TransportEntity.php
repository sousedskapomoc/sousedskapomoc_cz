<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;


/**
 * Class RoleEntity
 * @ORM\Entity
 */
class TransportEntity
{
    use Id;

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
     * @ORM\OneToMany(targetEntity="VolunteerEntity", mappedBy="transport")
     */
    protected $volunteers;
}
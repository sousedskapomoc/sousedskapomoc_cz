<?php


namespace SousedskaPomoc\Entities;

use Apolo\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\RoleEntity;


/**
 * Class VolunteerEntity
 * @ORM\Entity
 */
class VolunteerEntity
{
    use Id;

    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $personName;

    /**
     * @ORM\Column(type="string")
     */
    protected $personEmail;

    /**
     * @ORM\Column(type="string")
     */
    protected $personPhone;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $online;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="RoleEntity", inversedBy="users")
     */
    protected $roles;

    //@TODO - add transport, add address

}
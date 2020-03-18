<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;

/**
 * Class RoleEntity
 * @ORM\Entity
 */
class RoleEntity
{
    use Id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="VolunteerEntity", mappedBy="roles")
     */
    protected $users;
}
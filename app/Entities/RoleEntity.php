<?php


namespace SousedskaPomoc\Entities;

use Apolo\Entities\Traits\Timestampable;
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

    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="VolunteerEntity", mappedBy="roles")
     */
    protected $users;
}
<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;


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
}
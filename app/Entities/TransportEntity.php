<?php


namespace SousedskaPomoc\Entities;

use Apolo\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;


/**
 * Class TransportEntity
 * @ORM\Entity
 */
class TransportEntity
{
    use Id;

    use Timestampable;

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
<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;

/**
 * Class RoleEntity
 * @ORM\Entity
 */
class AddressEntity
{
    use Id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $locationId;


    /**
     * @ORM\Column(type="string")
     */
    protected $country;

    /**
     * @ORM\Column(type="string")
     */
    protected $state;

    /**
     * @ORM\Column(type="string")
     */
    protected $district;

    /**
     * @ORM\Column(type="string")
     */
    protected $city;

    /**
     * @ORM\Column(type="string")
     */
    protected $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity="VolunteerEntity", inversedBy="address")
     */
    protected $volunteers;
}
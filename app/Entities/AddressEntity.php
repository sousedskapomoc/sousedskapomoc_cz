<?php


namespace SousedskaPomoc\Entities;

use Apolo\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;
use SousedskaPomoc\Entities\DemandEntity;

/**
 * Class AddressEntity
 * @ORM\Entity
 */
class AddressEntity
{
    use Id;

    use Timestampable;

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

    /**
     * @ORM\OneToMany(targetEntity="DemandEntity", mappedBy="deliveryAddress")
     */
    protected $demandOrders;
}
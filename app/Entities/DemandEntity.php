<?php


namespace SousedskaPomoc\Entities;

use Apolo\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;
use SousedskaPomoc\Entities\AddressEntity;


/**
 * Class DemandEntity
 * @ORM\Entity
 */
class DemandEntity
{
    use Id;

    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="AddressEntity", inversedBy="demandOrders")
     * @ORM\Column(type="string")
     */
    protected $deliveryAddress;

    /**
     * @ORM\Column(type="string")
     */
    protected $items;

    /**
     * @ORM\Column(type="string")
     */
    protected $phone;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $processed;
}
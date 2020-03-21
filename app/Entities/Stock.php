<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\AddressEntity;


/**
 * Class StockEntity
 * @ORM\Entity
 */
class StockEntity
{
    use Id;

    /**
     * @ORM\ManyToOne(targetEntity="AddressEntity", inversedBy="stocks")
     */
    protected $stockAddress;

    /**
     * @ORM\Column(type="string")
     */
    protected $contactPhone;

    /**
     * @ORM\Column(type="string")
     */
    protected $openHours;
}
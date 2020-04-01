<?php

namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;

/**
 * Class Stock
 * @ORM\Entity
 */
class Stock
{
    use Id;

    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="stocks")
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

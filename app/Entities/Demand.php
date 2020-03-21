<?php


namespace SousedskaPomoc\Entities;

use SousedskaPomoc\Entities\Traits\Timestampable;
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

//    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="AddressEntity", inversedBy="demandOrders")
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



    /**
     * @return mixed
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }



    /**
     * @param mixed $deliveryAddress
     */
    public function setDeliveryAddress($deliveryAddress) : void
    {
        $this->deliveryAddress = $deliveryAddress;
    }



    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }



    /**
     * @param mixed $items
     */
    public function setItems($items) : void
    {
        $this->items = $items;
    }



    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }



    /**
     * @param mixed $phone
     */
    public function setPhone($phone) : void
    {
        $this->phone = $phone;
    }



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * @param mixed $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }



    /**
     * @return mixed
     */
    public function getProcessed()
    {
        return $this->processed;
    }



    /**
     * @param mixed $processed
     */
    public function setProcessed($processed) : void
    {
        $this->processed = $processed;
    }
}
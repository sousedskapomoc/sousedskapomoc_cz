<?php


namespace SousedskaPomoc\Entities;

use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\VolunteerEntity;
use SousedskaPomoc\Entities\AddressEntity;


/**
 * Class OrderEntity
 * @ORM\Entity
 */
class OrderEntity
{
    use Id;

//    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="AddressEntity", inversedBy="demandOrders")
     * @ORM\Column(type="string")
     */
    protected $pickupAddress;

    /**
     * @ORM\Column(type="string")
     */
    protected $deliveryAddress;

    /**
     * @ORM\ManyToOne(targetEntity="VolunteerEntity", inversedBy="createdOrders")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="VolunteerEntity", inversedBy="deliveredOrders")
     */
    protected $courier;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status;



    /**
     * @return mixed
     */
    public function getPickupAddress()
    {
        return $this->pickupAddress;
    }



    /**
     * @param mixed $pickupAddress
     */
    public function setPickupAddress($pickupAddress) : void
    {
        $this->pickupAddress = $pickupAddress;
    }



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
    public function getAuthor()
    {
        return $this->author;
    }



    /**
     * @param mixed $author
     */
    public function setAuthor($author) : void
    {
        $this->author = $author;
    }



    /**
     * @return mixed
     */
    public function getCourier()
    {
        return $this->courier;
    }



    /**
     * @param mixed $courier
     */
    public function setCourier($courier) : void
    {
        $this->courier = $courier;
    }



    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * @param mixed $status
     */
    public function setStatus($status) : void
    {
        $this->status = $status;
    }
}
<?php


namespace SousedskaPomoc\Entities;

use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Orders
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
{
    use Id;

    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="ordersPickup", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $pickupAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="ordersDelivery", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $deliveryAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Volunteer", inversedBy="createdOrders", cascade={"persist"})
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Volunteer", inversedBy="deliveredOrders", cascade={"persist"})
     */
    protected $courier;

    /**
     * @ORM\Column(type="string")
     */
    protected $stat;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $customerNote;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $courierNote;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $deliveryPhone;

    /**
     * @ORM\Column(type="text")
     */
    protected $items;

    /**
     * @ORM\OneToOne(targetEntity="Demand", mappedBy="createdOrder", cascade={"persist"})
     */
    protected $fromDemand;


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
    public function setItems($items): void
    {
        $this->items = $items;
    }


    /**
     * @return mixed
     */
    public function getDeliveryPhone()
    {
        return $this->deliveryPhone;
    }


    /**
     * @param mixed $deliveryPhone
     */
    public function setDeliveryPhone($deliveryPhone): void
    {
        $this->deliveryPhone = $deliveryPhone;
    }


    /**
     * @return mixed
     */
    public function getCustomerNote()
    {
        return $this->customerNote;
    }


    /**
     * @param mixed $customerNote
     */
    public function setCustomerNote($customerNote): void
    {
        $this->customerNote = $customerNote;
    }


    /**
     * @return mixed
     */
    public function getCourierNote()
    {
        return $this->courierNote;
    }


    /**
     * @param mixed $courierNote
     */
    public function setCourierNote($courierNote): void
    {
        $this->courierNote = $courierNote;
    }


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
    public function setPickupAddress($pickupAddress): void
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
    public function setDeliveryAddress($deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
    }


    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }


    /**
     * @param mixed $author
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
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
    public function setCourier($courier): void
    {
        $this->courier = $courier;
    }


    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->stat;
    }


    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->stat = $status;
    }

    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getFromDemand()
    {
        return $this->fromDemand;
    }



    /**
     * @param mixed $fromDemand
     */
    public function setFromDemand(Demand $fromDemand) : void
    {
        $this->fromDemand = $fromDemand;
        $fromDemand->setCreatedOrder($this);
    }


}

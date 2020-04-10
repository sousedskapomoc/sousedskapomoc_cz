<?php


namespace SousedskaPomoc\Entities;

use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Demand
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\DemandRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Demand
{
    use Id;

    use Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="demandOrders")
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
     * @ORM\Column(type="string")
     */
    protected $processed;

    /**
     * @ORM\OneToOne(targetEntity="Order", inversedBy="fromDemand")
     */
    protected $createdOrder;


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
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
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
    public function setName($name): void
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
    public function setProcessed($processed): void
    {
        $this->processed = $processed;
    }



    /**
     * @return mixed
     */
    public function getCreatedOrder()
    {
        return $this->createdOrder;
    }



    /**
     * @param mixed $createdOrder
     */
    public function setCreatedOrder($createdOrder) : void
    {
        $this->createdOrder = $createdOrder;
    }


}

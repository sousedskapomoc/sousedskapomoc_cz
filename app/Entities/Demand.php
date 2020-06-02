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
     * @ORM\ManyToOne(targetEntity="Volunteer", inversedBy="deliveredOrders", cascade={"persist"})
     */
    protected $courier;

    /**
     * @ORM\Column(type="string")
     */
    protected $deliveryPhone;

    /**
     * @ORM\Column(type="string")
     */
    protected $deliveryName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contactName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contactPhone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $food;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $medicine;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $veils;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $other;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $organizationName;

    /**
     * @ORM\Column(type="string")
     */
    protected $processed;

    /**
     * @ORM\OneToOne(targetEntity="Order", inversedBy="fromDemand")
     */
    protected $createdOrder;

    /**
     * @ORM\Column(type="boolean",options={"default" : 0})
     */
    protected $isContactPerson;

    /**
     * @ORM\Column(type="boolean",options={"default" : 0})
     */
    protected $isOrganization;


    /**
     * @return mixed
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }


    /**
     * @param mixed $organizationName
     */
    public function setOrganizationName($organizationName): void
    {
        $this->organizationName = $organizationName;
    }


    /**
     * @return mixed
     */
    public function getIsContactPerson()
    {
        return $this->isContactPerson;
    }


    /**
     * @param mixed $isContactPerson
     */
    public function setIsContactPerson($isContactPerson): void
    {
        $this->isContactPerson = $isContactPerson;
    }


    /**
     * @return mixed
     */
    public function getIsOrganization()
    {
        return $this->isOrganization;
    }


    /**
     * @param mixed $isOrganization
     */
    public function setIsOrganization($isOrganization): void
    {
        $this->isOrganization = $isOrganization;
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
    public function getDeliveryName()
    {
        return $this->deliveryName;
    }


    /**
     * @param mixed $deliveryName
     */
    public function setDeliveryName($deliveryName): void
    {
        $this->deliveryName = $deliveryName;
    }


    /**
     * @return mixed
     */
    public function getContactName()
    {
        return $this->contactName;
    }


    /**
     * @param mixed $contactName
     */
    public function setContactName($contactName): void
    {
        $this->contactName = $contactName;
    }


    /**
     * @return mixed
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }


    /**
     * @param mixed $contactPhone
     */
    public function setContactPhone($contactPhone): void
    {
        $this->contactPhone = $contactPhone;
    }


    /**
     * @return mixed
     */
    public function getFood()
    {
        return $this->food;
    }


    /**
     * @param mixed $food
     */
    public function setFood($food): void
    {
        $this->food = $food;
    }


    /**
     * @return mixed
     */
    public function getMedicine()
    {
        return $this->medicine;
    }


    /**
     * @param mixed $medicine
     */
    public function setMedicine($medicine): void
    {
        $this->medicine = $medicine;
    }


    /**
     * @return mixed
     */
    public function getVeils()
    {
        return $this->veils;
    }


    /**
     * @param mixed $veils
     */
    public function setVeils($veils): void
    {
        $this->veils = $veils;
    }


    /**
     * @return mixed
     */
    public function getOther()
    {
        return $this->other;
    }


    /**
     * @param mixed $other
     */
    public function setOther($other): void
    {
        $this->other = $other;
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
    public function setCreatedOrder($createdOrder): void
    {
        $this->createdOrder = $createdOrder;
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
}

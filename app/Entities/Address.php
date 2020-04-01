<?php


namespace SousedskaPomoc\Entities;

use Nettrine\ORM\Entity\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;
use SousedskaPomoc\Entities\Traits\Timestampable;

/**
 * Class Address
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\AddressRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Address
{
    use Id;

    use Timestampable;


    /**
     * @ORM\Column(type="string")
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

    /** @ORM\Column(type="string", nullable=true) */
    protected $street;

    /** @ORM\Column(type="string", nullable=true) */
    protected $houseNumber;

    /**
     * @ORM\OneToOne(targetEntity="Volunteer", mappedBy="address")
     */
    protected $volunteer;

    /**
     * @ORM\OneToMany(targetEntity="Demand", mappedBy="deliveryAddress")
     */
    protected $demandOrders;

    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="stockAddress")
     */
    protected $stocks;

    /**
     * @ORM\OneToOne(targetEntity="Order", mappedBy="pickupAddress")
     */
    protected $orderPickup;

    /**
     * @ORM\OneToOne(targetEntity="Order", mappedBy="deliveryAddress")
     */
    protected $orderDelivery;


    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }


    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }


    /**
     * @return mixed
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }


    /**
     * @param mixed $houseNumber
     */
    public function setHouseNumber($houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }


    /**
     * @return mixed
     */
    public function getStocks()
    {
        return $this->stocks;
    }


    /**
     * @param mixed $stocks
     */
    public function setStocks($stocks): void
    {
        $this->stocks = $stocks;
    }


    /**
     * @return mixed
     */
    public function getLocationId()
    {
        return $this->locationId;
    }


    /**
     * @param mixed $locationId
     */
    public function setLocationId($locationId): void
    {
        $this->locationId = $locationId;
    }


    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }


    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }


    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }


    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }


    /**
     * @param mixed $district
     */
    public function setDistrict($district): void
    {
        $this->district = $district;
    }


    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }


    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }


    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }


    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }


    /**
     * @return mixed
     */
    public function getVolunteer()
    {
        return $this->volunteer;
    }


    /**
     * @param mixed $volunteer
     */
    public function setVolunteer($volunteer): void
    {
        $this->volunteer = $volunteer;
    }


    /**
     * @return mixed
     */
    public function getDemandOrders()
    {
        return $this->demandOrders;
    }


    /**
     * @param mixed $demandOrders
     */
    public function setDemandOrders($demandOrders): void
    {
        $this->demandOrders = $demandOrders;
    }
}

<?php


namespace SousedskaPomoc\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Entities\Demand;

/**
 * Class AddressEntity
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Address
{
    use Id;


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
     * @ORM\ManyToOne(targetEntity="Volunteer", inversedBy="address")
     */
    protected $volunteers;

    /**
     * @ORM\OneToMany(targetEntity="Demand", mappedBy="deliveryAddress")
     */
    protected $demandOrders;

    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="stockAddress")
     */
    protected $stocks;



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
    public function setLocationId($locationId) : void
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
    public function setCountry($country) : void
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
    public function setState($state) : void
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
    public function setDistrict($district) : void
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
    public function setCity($city) : void
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
    public function setPostalCode($postalCode) : void
    {
        $this->postalCode = $postalCode;
    }



    /**
     * @return mixed
     */
    public function getVolunteers()
    {
        return $this->volunteers;
    }



    /**
     * @param mixed $volunteers
     */
    public function setVolunteers($volunteers) : void
    {
        $this->volunteers = $volunteers;
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
    public function setDemandOrders($demandOrders) : void
    {
        $this->demandOrders = $demandOrders;
    }
}
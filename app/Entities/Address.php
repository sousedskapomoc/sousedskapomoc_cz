<?php


namespace SousedskaPomoc\Entities;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $district;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $postalCode;

    /** @ORM\Column(type="string", nullable=true) */
    protected $street;

    /** @ORM\Column(type="string", nullable=true) */
    protected $houseNumber;

    /** @ORM\Column(type="string") */
    protected $longitude;

    /** @ORM\Column(type="string") */
    protected $latitude;

    /**
     * @ORM\OneToMany(targetEntity="Volunteer", mappedBy="address", cascade={"persist"})
     */
    protected $volunteers;

    /**
     * @ORM\OneToMany(targetEntity="Demand", mappedBy="deliveryAddress", cascade={"persist"})
     */
    protected $demandOrders;

    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="stockAddress", cascade={"persist"})
     */
    protected $stocks;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="pickupAddress", cascade={"persist"})
     */
    protected $ordersPickup;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="deliveryAddress", cascade={"persist"})
     */
    protected $ordersDelivery;


    public function __construct()
    {
        $this->demandOrders = new ArrayCollection();
        $this->ordersPickup = new ArrayCollection();
        $this->ordersDelivery = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->volunteers = new ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }



    /**
     * @param mixed $longtitude
     */
    public function setLongitude($longitude) : void
    {
        $this->longitude = $longitude;
    }



    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }



    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude) : void
    {
        $this->latitude = $latitude;
    }



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
    public function getVolunteers()
    {
        return $this->volunteers;
    }


    /**
     * @return mixed
     */
    public function getDemandOrders()
    {
        return $this->demandOrders;
    }


    public function addDemandOrder($demandOrder) :self
    {
        if (!$this->demandOrders->contains($demandOrder)) {
            $this->demandOrders[] = $demandOrder;

            /** @var \SousedskaPomoc\Entities\Demand $demandOrders */
            $demandOrder->setDeliveryAddress($this);
        }
        return $this;
    }

    public function removeDemandOrder($demandOrder): self
    {
        if ($this->demandOrders->contains($demandOrder)) {
            $this->demandOrders->removeElement($demandOrder);

            // set the owning side to null (unless already changed)
            /** @var \SousedskaPomoc\Entities\Demand $demandOrder */
            if ($demandOrder->getDeliveryAddress() === $this) {
                $demandOrder->setDeliveryAddress(null);
            }
        }
        return $this;
    }

    public function addPickupOrder($order) :self
    {
        if (!$this->ordersPickup->contains($order)) {
            $this->ordersPickup[] = $order;

            /** @var \SousedskaPomoc\Entities\Order $order */
            $order->setPickupAddress($this);
        }
        return $this;
    }

    public function removePickupOrder($order): self
    {
        if ($this->ordersPickup->contains($order)) {
            $this->ordersPickup->removeElement($order);

            // set the owning side to null (unless already changed)
            /** @var \SousedskaPomoc\Entities\Order $order */
            if ($order->getPickupAddress() === $this) {
                $order->setPickupAddress(null);
            }
        }
        return $this;
    }

    public function addDeliveryOrder($order) :self
    {
        if (!$this->ordersDelivery->contains($order)) {
            $this->ordersDelivery[] = $order;

            /** @var \SousedskaPomoc\Entities\Order $order */
            $order->setDeliveryAddress($this);
        }
        return $this;
    }

    public function removeDeliveryOrder($order): self
    {
        if ($this->ordersDelivery->contains($order)) {
            $this->ordersDelivery->removeElement($order);

            // set the owning side to null (unless already changed)
            /** @var \SousedskaPomoc\Entities\Order $order */
            if ($order->getDeliveryAddress() === $this) {
                $order->setDeliveryAddress(null);
            }
        }
        return $this;
    }

    public function addVolunteer($user) :self
    {
        if (!$this->volunteers->contains($user)) {
            $this->volunteers[] = $user;

            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            $user->setAddress($this);
        }
        return $this;
    }

    public function removeVolunteer($user): self
    {
        if ($this->volunteers->contains($user)) {
            $this->volunteers->removeElement($user);

            // set the owning side to null (unless already changed)
            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            if ($user->getAddress() === $this) {
                $user->setAddress(null);
            }
        }
        return $this;
    }
}

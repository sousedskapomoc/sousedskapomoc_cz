<?php


namespace SousedskaPomoc\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;

/**
 * Class Volunteer
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\VolunteerRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Volunteer
{
    use Id;

    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $personName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $uploadPhoto;
    /**
     * @ORM\Column(type="string")
     */
    protected $personEmail;

    /**
     * @ORM\Column(type="string")
     */
    protected $personPhone;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $online;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users", cascade={"persist"})
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="volunteer", cascade={"persist"})
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="Transport", inversedBy="volunteers")
     */
    protected $transport;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="owner", cascade={"persist"})
     */
    protected $createdOrders;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="courier", cascade={"persist"})
     */
    protected $deliveredOrders;

    /**
     * @ORM\OneToMany(targetEntity="Demand", mappedBy="courier", cascade={"persist"})
     */
    protected $deliveredDemands;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $hash;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $note;



    public function __construct()
    {
        $this->deliveredOrders = new ArrayCollection();
        $this->deliveredDemands = new ArrayCollection();
        $this->createdOrders = new ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }



    /**
     * @param mixed $hash
     */
    public function setHash($hash) : void
    {
        $this->hash = $hash;
    }



    /**
     * @return mixed
     */
    public function getPersonName()
    {
        return $this->personName;
    }



    /**
     * @param mixed $personName
     */
    public function setPersonName($personName) : void
    {
        $this->personName = $personName;
    }



    /**
     * @return mixed
     */
    public function getPersonEmail()
    {
        return $this->personEmail;
    }



    /**
     * @param mixed $personEmail
     */
    public function setPersonEmail($personEmail) : void
    {
        $this->personEmail = $personEmail;
    }



    /**
     * @return mixed
     */
    public function getPersonPhone()
    {
        return $this->personPhone;
    }



    /**
     * @param mixed $personPhone
     */
    public function setPersonPhone($personPhone) : void
    {
        $this->personPhone = $personPhone;
    }



    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }



    /**
     * @param mixed $active
     */
    public function setActive($active) : void
    {
        $this->active = $active;
    }



    /**
     * @return mixed
     */
    public function getOnline()
    {
        return $this->online;
    }



    /**
     * @param mixed $online
     */
    public function setOnline($online) : void
    {
        $this->online = $online;
    }



    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }



    /**
     * @param mixed $password
     */
    public function setPassword($password) : void
    {
        $this->password = $password;
    }



    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }



    public function setRole($role)
    {
        $this->role = $role;
    }



    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }



    /**
     * @param mixed $address
     */
    public function setAddress($address) : void
    {
        $this->address = $address;
    }



    /**
     * @return mixed
     */
    public function getTransport()
    {
        return $this->transport;
    }



    /**
     * @param mixed $transport
     */
    public function setTransport($transport) : void
    {
        $this->transport = $transport;
    }



    /**
     * @return mixed
     */
    public function getCreatedOrders()
    {
        return $this->createdOrders;
    }



    /**
     * @param mixed $order
     */
    public function addCreatedOrder($order) : self
    {
        if (!$this->createdOrders->contains($order)) {
            $this->createdOrders[] = $order;

            $order->setOwner($this);
        }

        return $this;
    }



    public function removeCreatedOrder($order) : self
    {
        if ($this->createdOrders) {
            if ($this->createdOrders->contains($order)) {
                $this->createdOrders->removeElement($order);
                /** @var Order $order */
                $order->setOwner(null);
            } else {
                $order->setOwner(null);
            }
        }

        return $this;
    }



    /**
     * @return mixed
     */
    public function getDeliveredOrders()
    {
        return $this->deliveredOrders;
    }



    /**
     * @param mixed $order
     *
     * @return Volunteer
     */
    public function addDeliveredOrder($order) : self
    {
        if ($this->deliveredOrders) {
            if (!$this->deliveredOrders->contains($order)) {
                $this->deliveredOrders[] = $order;
            }
            /** @var Order $order */
            $order->setCourier($this);
        } else {
            $this->deliveredOrders[] = $order;
            /** @var Order $order */
            $order->setCourier($this);
        }

        return $this;
    }



    public function removeDeliveredOrder($order) : self
    {
        if ($this->deliveredOrders) {
            if ($this->deliveredOrders->contains($order)) {
                $this->deliveredOrders->removeElement($order);
                /** @var Order $order */
                $order->setCourier(null);
            } else {
                $order->setCourier(null);
            }
        } else {
            $order->setCourier(null);
        }

        return $this;
    }



    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getUploadPhoto()
    {
        return $this->uploadPhoto;
    }



    /**
     * @param mixed $uploadPhoto
     */
    public function setUploadPhoto($uploadPhoto) : void
    {
        $this->uploadPhoto = $uploadPhoto;
    }



    /**
     * @param mixed $demand
     *
     * @return Volunteer
     */
    public function addDeliveredDemand($demand) : self
    {
        if ($this->deliveredDemands) {
            if (!$this->deliveredDemands->contains($demand)) {
                $this->deliveredDemands[] = $demand;
            }
            /** @var Demand $demand */
            $demand->setCourier($this);
        } else {
            $this->deliveredDemands[] = $demand;
            /** @var Demand $demand */
            $demand->setCourier($this);
        }

        return $this;
    }



    public function removeDeliveredDemand($demand) : self
    {
        if ($this->deliveredDemands) {
            if ($this->deliveredDemands->contains($demand)) {
                $this->deliveredDemands->removeElement($demand);
                /** @var Demand $demand */
                $demand->setCourier(null);
            } else {
                $demand->setCourier(null);
            }
        } else {
            $demand->setCourier(null);
        }

        return $this;
    }



    public function setNote($note)
    {
        $this->note = $note;
    }



    public function getNote()
    {
        return $this->note;
    }
}

<?php


namespace SousedskaPomoc\Entities;

use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\RoleEntity;
use SousedskaPomoc\Entities\AddressEntity;
use SousedskaPomoc\Entities\TransportEntity;


/**
 * Class VolunteerEntity
 * @ORM\Entity
 */
class VolunteerEntity
{
    use Id;

//    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $personName;

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
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="RoleEntity", inversedBy="users")
     */
    protected $roles;

    /**
     * @ORM\OneToMany(targetEntity="AddressEntity", mappedBy="volunteers")
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="TransportEntity", inversedBy="volunteers")
     */
    protected $transport;

    /**
     * @ORM\OneToMany(targetEntity="OrderEntity", mappedBy="author")
     */
    protected $createdOrders;

    /**
     * @ORM\OneToMany(targetEntity="OrderEntity", mappedBy="courier")
     */
    protected $deliveredOrders;



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
    public function getRoles()
    {
        return $this->roles;
    }



    /**
     * @param mixed $roles
     */
    public function setRoles($roles) : void
    {
        $this->roles = $roles;
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
     * @param mixed $createdOrders
     */
    public function setCreatedOrders($createdOrders) : void
    {
        $this->createdOrders = $createdOrders;
    }



    /**
     * @return mixed
     */
    public function getDeliveredOrders()
    {
        return $this->deliveredOrders;
    }



    /**
     * @param mixed $deliveredOrders
     */
    public function setDeliveredOrders($deliveredOrders) : void
    {
        $this->deliveredOrders = $deliveredOrders;
    }
}
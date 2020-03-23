<?php


namespace SousedskaPomoc\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Volunteer;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\RoleRepository")
 */
class Role
{
    use Id;

//    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Volunteer", mappedBy="roles")
     */
    protected $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(Volunteer $user)
    {
        $this->users->add($user);
    }

    public function removeUser(Volunteer $user) {
        $this->users->removeElement($user);
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
    public function getUsers()
    {
        return $this->users;
    }



    /**
     * @param mixed $users
     */
    public function setUsers($users) : void
    {
        $this->users = $users;
    }
}
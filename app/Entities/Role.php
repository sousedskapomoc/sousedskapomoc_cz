<?php

namespace SousedskaPomoc\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\RoleRepository")
 */
class Role
{
    use Id;

    use Timestampable;

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

    public function addUsers(Volunteer $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;

            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            $user->setRole($this);
        }
        return $this;
    }

    public function removeUsers(Volunteer $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);

            // set the owning side to null (unless already changed)
            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }
        return $this;
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
    public function getUsers()
    {
        return $this->users;
    }
}

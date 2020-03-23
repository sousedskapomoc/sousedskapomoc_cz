<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Volunteer;


class AddressRepository extends DoctrineEntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }


    public function getByTown($town) {
        return $this->findBy(['city'=>$town]);
    }

    public function countUniqueTowns() {
        //@TODO - count uniqueTowns
        return $this->findBy([]);
    }


    public function getByLocationId($locationId)
    {
        return $this->findOneBy(['locationId' => $locationId]);
    }

    public function create(Address $address) {
        $addr = $this->getByLocationId($address->getLocationId());
        if ($addr instanceof Address) {
            return $addr;
        } else {
            $em = $this->getEntityManager();
            $em->persist($address);
            $em->flush();
            return $address;
        }
    }

    public function updateVolunteers($locationId, Volunteer $user) {
        $dbAddress = $this->getByLocationId($locationId);
        if ($dbAddress instanceof Address) {
            $dbAddress->addVolunteers($user);
        }
    }



    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function getAll()
    {
        return $this->findBy([]);
    }



    public function getAllAsArray()
    {
        $roles = $this->findBy([]);
        $arrayRoles = [];
        foreach ($roles as $r) {
            $arrayRoles[$r->getId()] = $r->getName();
        }

        return $arrayRoles;
    }
}
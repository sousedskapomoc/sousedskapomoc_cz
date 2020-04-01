<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class RoleRepository extends DoctrineEntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function countUsers(string $name)
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->getByName($name);
        return sizeof($role->getUsers());
    }

    public function getByName($name)
    {
        return $this->findOneBy(['name' => $name]);
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

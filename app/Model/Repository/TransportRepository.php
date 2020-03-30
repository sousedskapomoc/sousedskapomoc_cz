<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;


class TransportRepository extends DoctrineEntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }



    public function getByType($type)
    {
        return $this->findOneBy(['type' => $type]);
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
            $arrayRoles[$r->getId()] = $r->getType();
        }

        return $arrayRoles;
    }
}
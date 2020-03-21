<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;


class VolunteerRepository extends DoctrineEntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }



    public function getByEmail($email)
    {
        return $this->findOneBy(['personEmail' => $email]);
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
}
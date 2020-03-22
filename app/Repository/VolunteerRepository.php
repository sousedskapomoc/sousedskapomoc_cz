<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Volunteer;


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

    public function update($id, Volunteer $user) {
        /** @var Volunteer $dbUser */
        $dbUser = $this->getById($id);
        if ($user instanceof Volunteer) {
            if ($user->getPassword() != null) {
                $dbUser->setPassword($user->getPassword());
            }
            $dbUser->setPersonName($user->getPersonName());
            $dbUser->setPersonEmail($user->getPersonEmail());
            $dbUser->setPersonPhone($user->getPersonPhone());

            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();
        }

    }
}
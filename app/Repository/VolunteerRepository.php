<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Entities\Volunteer;

class VolunteerRepository extends DoctrineEntityRepository
{
    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getCourierByTown($town)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        SELECT
        u
        FROM
        SousedskaPomoc\Entities\Volunteer u JOIN u.address x
        WHERE
        x.city = '$town'
        ");
        return $query->getResult();
    }


    public function getTownForUser($id)
    {
        /** @var \SousedskaPomoc\Entities\Address $address */
        $address = $this->getById($id)->getAddress();
        return $address->getCity();
    }

    public function getByEmail($email)
    {
        return $this->findOneBy(['personEmail' => $email]);
    }

    public function setPass($id, $password)
    {
        /** @var Volunteer $user */
        $user = $this->getById($id);

        $user->setPassword($password);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function getUserByHash($hash)
    {
        return $this->findOneBy(['hash' => $hash]);
    }

    public function fetchTotalCount()
    {
        return $this->count([]);
    }

    public function getNonActiveUsers()
    {
        return $this->count(['password' => null]);
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

    public function setOnline($id, $active)
    {
        /** @var Volunteer $user */
        $user = $this->getById($id);
        $user->setOnline($active);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }


    public function update($id, Volunteer $user)
    {
        /** @var Volunteer $dbUser */
        $dbUser = $this->getById($id);
        if ($dbUser instanceof Volunteer) {
            if ($user->getPassword() != null) {
                $dbUser->setPassword($user->getPassword());
            }
            $dbUser->setPersonName($user->getPersonName());
            $dbUser->setPersonEmail($user->getPersonEmail());
            $dbUser->setPersonPhone($user->getPersonPhone());

            foreach ($dbUser->getRole() as $role) {
                $dbUser->removeRole($role);
            }

            foreach ($user->getRole() as $role) {
                $dbUser->addRole($role);
            }

            $em = $this->getEntityManager();
            $em->persist($dbUser);
            $em->flush();
        }
    }

    public function save($user) {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function isOnline($id)
    {
        /** @var Volunteer $user */
        $user = $this->getById($id);
        return $user->getOnline();
    }

    public function register(Volunteer $user)
    {
        $dbUser = $this->getByEmail($user->getPersonEmail());
        if ($dbUser instanceof Volunteer) {
            throw new AuthenticationException('This e-mail address is already registred.');
        } else {
            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();
        }
    }
}

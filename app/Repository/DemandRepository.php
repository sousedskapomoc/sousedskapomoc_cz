<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Demand;

class DemandRepository extends DoctrineEntityRepository
{
    public function getById($id) {
        return $this->findOneBy(['id'=>$id]);
    }

    public function getAll() {
        return $this->findBy([]);
    }

    public function create(Demand $demand) {
        $em = $this->getEntityManager();
        $em->persist($demand);
        $em->flush();
    }

    public function setProcessed($id, bool $processed) {
        /** @var Demand $demand */
        $demand = $this->findOneBy(['id'=>$id]);
        $demand->setProcessed($processed);
        $em = $this->getEntityManager();
        $em->persist($demand);
        $em->flush();
    }
}

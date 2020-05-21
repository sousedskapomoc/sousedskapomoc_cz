<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class CallRouletteRepository extends DoctrineEntityRepository
{
    public function store(\SousedskaPomoc\Entities\CallRoulette $callRoulette)
    {
        $em = $this->getEntityManager();
        $em->persist($callRoulette);
        $em->flush();
    }
}

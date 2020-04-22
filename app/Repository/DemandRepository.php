<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Entities\Volunteer;

class DemandRepository extends DoctrineEntityRepository
{
    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getAll()
    {
        return $this->findBy([]);
    }

    public function create(Demand $demand)
    {
        $em = $this->getEntityManager();
        $em->persist($demand);
        $em->flush();
    }

    public function setProcessed($id, $processed)
    {
        /** @var Demand $demand */
        $demand = $this->findOneBy(['id' => $id]);
        $demand->setProcessed($processed);
        $em = $this->getEntityManager();
        $em->persist($demand);
        $em->flush();
    }

    public function getAllUnprocessed()
    {
        return $this->findBy(['processed' => 'new']);
    }

    public function getUnprocessedByTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')
            ->from('\SousedskaPomoc\Entities\Demand', 'd')
            ->leftJoin('d.deliveryAddress', 'a')
            ->setParameter('town', $town)
            ->setParameter('processed', 'new')
            ->andWhere("a.city = :town")
            ->andWhere("d.processed = :processed");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getByUser($id)
    {
        return $this->findBy(['courier' => $id]);
    }

    public function getAllForGrid()
    {
        $dataset = [];

        /** @var Demand $demand */
        foreach ($this->getAll() as $demand) {
            if ($demand->getDeliveryAddress() !== null) {
                /** @var Address $city */
                $city = $demand->getDeliveryAddress()->getFullAddress();
            }

            $dataset[] = [
                'id' => $demand->getId(),
                'deliveryAddress' => $city,
                'processed' => $demand->getProcessed(),
                'contactName' => $demand->getContactName(),
                'contactPhone' => $demand->getContactPhone(),
                'organizationName' => $demand->getOrganizationName(),
                'deliveryName' => $demand->getDeliveryName(),
                'deliveryPhone' => $demand->getDeliveryPhone(),
                'createdAt' => $demand->getCreatedAt()
            ];

            $city = null;
        }

        return $dataset;
    }

    public function assignDemand(Volunteer $volunteer, Demand $demand)
    {
        $demand->setCourier($volunteer);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($demand);
        $entityManager->flush();
    }
}

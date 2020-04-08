<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Entities\Volunteer;

class OrderRepository extends DoctrineEntityRepository
{
    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getByUser($id)
    {
        return $this->findBy(['author' => $id]);
    }


    public function fetchCount()
    {
        return $this->count([]);
    }

    public function getAllLive($id)
    {
        $this->findby(['courier' => $id]);
    }

    public function getAllInTownByStatus($town, $status)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        SELECT
        o
        FROM
        SousedskaPomoc\Entities\Orders o JOIN o.deliveryAddress x
        WHERE
        x.city = '$town'
        AND
        o.stat = '$status'
        ");
        return $query->getResult();
    }

    public function getAllLiveInTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->setParameter('town', $town)
            ->andWhere("a.city = :town")
            ->andWhere("o.stat IN ('assigned','picking','delivering')");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function updateStatus($id, $active)
    {
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $id]);
        if ($order instanceof Order) {
            $order->setStatus($active);
            $em = $this->getEntityManager();
            $em->persist($order);
            $em->flush();
        } else {
            throw new \Exception('Order not found.');
        }
    }

    public function findAllForUser($userId)
    {
        return $this->findBy(['owner' => $userId]);
    }

    public function findAllForCourier($userId)
    {
        return $this->findBy(['courier' => $userId]);
    }

    public function findAllNew()
    {
        return $this->findBy(['stat' => 'new']);
    }

    public function changeStatus($orderId, $status)
    {
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $orderId]);
        if ($order instanceof Order) {
            $order->setStatus($status);
            $em = $this->getEntityManager();
            $em->persist($order);
            $em->flush();
        } else {
            throw new \Exception('Order not found.');
        }
    }

    public function updateNote($orderId, $note)
    {
        /** @var Order $order */
        $order = $this->findBy(['id' => $orderId]);
        if ($order instanceof Order) {
            $order->setCourierNote($note);
            $em = $this->getEntityManager();
            $em->persist($order);
            $em->flush();
        } else {
            throw new \Exception('Order not found.');
        }
    }

    public function findAllLive()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->Where("o.stat IN ('assigned','picking','delivering')");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllLiveByCourierByTown($town, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->setParameter('courier', $userId)
            ->where("o.courier = :courier")
            ->setParameter('town', $town)
            ->andWhere("a.city = :town")
            ->andWhere("o.stat IN ('assigned','picking','delivering')");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllDelivered()
    {
        return $this->findBy(['stat' => 'delivered']);
    }

    public function assignOrder($courier, $order_id)
    {
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $order_id]);
        if ($courier instanceof Volunteer) {
            $order->setStatus('assigned');
            $courier->addDeliveredOrder($order);
            $em = $this->getEntityManager();
            $em->persist($courier);
            $em->flush();
        } else {
            $order->setStatus('new');
            /** @var Volunteer $dbCourier */
            $dbCourier = $order->getCourier();
            $dbCourier->removeDeliveredOrder($order);
            $em = $this->getEntityManager();
            $em->persist($dbCourier);
            $em->flush();
        }
    }

    public function findAllNewInTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->where("o.stat = 'new'")
            ->setParameter('town', $town)
            ->andWhere("a.city = :town");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllLiveInTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->where("o.stat IN ('assigned', 'picking', 'delivering')")
            ->setParameter('town', $town)
            ->andWhere("a.city = :town");
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllDeliveredInTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->where("o.stat = 'delivered'")
            ->setParameter('town', $town)
            ->andWhere("a.city = :town");
        $query = $qb->getQuery();
        return $query->getResult();
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


    public function create(Order $order)
    {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }


    public function update(Order $dbOrder, Order $tmpOrder)
    {
        $dbOrder->setCourier($tmpOrder->getCourier());
        $dbOrder->setDeliveryAddress($tmpOrder->getDeliveryAddress());
        $dbOrder->setPickupAddress($tmpOrder->getPickupAddress());
        $dbOrder->setStatus($tmpOrder->getStatus());
        $dbOrder->setCourierNote($tmpOrder->getCourierNote());
        $dbOrder->setCustomerNote($tmpOrder->setCustomerNote());

        $em = $this->getEntityManager();
        $em->persist($dbOrder);
        $em->flush();
    }


    public function upsert(Order $order)
    {
        $localOrder = $this->getById($order->getId());
        if ($localOrder instanceof Order) {
            $this->update($localOrder, $order);
        } else {
            $this->create($order);
        }
    }

    public function updateCourierNote($id, $note)
    {
        $order = $this->getById($id);
        if ($order instanceof Order) {
            $order->setCourierNote($note);

            $em = $this->getEntityManager();
            $em->persist($order);
            $em->flush();
        } else {
            throw new \Exception('Orders not found.');
        }
    }

    public function removeCourier($orderId)
    {
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $orderId]);
        /** @var Volunteer $courier */
        $courier = $order->getCourier();
        $courier->removeDeliveredOrder($order);
        $em = $this->getEntityManager();
        $em->persist($courier);
        $em->flush();
    }

    public function remove($id)
    {
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $id]);
        $order->setStatus('archived');
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    public function fetchDeliveredCount()
    {
        return $this->count(['stat' => 'delivered']);
    }
}

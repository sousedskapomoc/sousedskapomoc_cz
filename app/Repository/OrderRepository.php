<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Order;

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
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        SELECT
        o
        FROM
        SousedskaPomoc\Entities\Orders o JOIN o.deliveryAddress x
        WHERE
        x.city = '$town'
        AND (o.stat = 'delivering' OR o.stat = 'picking' OR o.stat = 'assigned')
        ");
        return $query->getResult();
    }

    public function assignOrder($orderId, $courierId)
    {
        /** @var Orders $order */
        $order = $this->getById($orderId);
        $order->setCourier($courierId);
        $order->setStatus('assigned');
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    public function updateStatus($id, $active)
    {
        /** @var Orders $order */
        $order = $this->getById($id);
        $order->setStatus($active);
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    public function findAllForUser($userId)
    {
        return $this->findBy(['author' => $userId]);
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
        if ($localOrder instanceof Orders) {
            $this->update($localOrder, $order);
        } else {
            $this->create($order);
        }
    }

    public function updateCourierNote($id, $note)
    {
        $order = $this->getById($id);
        if ($order instanceof Orders) {
            $order->setCourierNote($note);

            $em = $this->getEntityManager();
            $em->persist($order);
            $em->flush();
        } else {
            throw new \Exception('Orders not found.');
        }
    }
}

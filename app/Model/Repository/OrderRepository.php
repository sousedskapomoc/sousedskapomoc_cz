<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\Orders;
use SousedskaPomoc\Entities\Volunteer;


class OrderRepository extends DoctrineEntityRepository
{

    public function getById($id)
    {
        return $this->findOneBy(['id' => $id]);
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



    public function create(Orders $order)
    {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }



    public function update(Orders $dbOrder, Orders $tmpOrder)
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



    public function upsert(Orders $order)
    {
        $localOrder = $this->getById($order->getId());
        if ($localOrder instanceof Orders) {
            $this->update($localOrder, $order);
        } else {
            $this->create($order);
        }
    }

    public function updateCourierNote($id, $note) {
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
<?php

namespace SousedskaPomoc\Repository;

use Cassandra\Date;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Nette\Utils\DateTime;
use SousedskaPomoc\Entities\Address;
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
        return $this->findBy(['owner' => $id]);
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



    public function assignOrderCoordinator($order_id, Volunteer $user)
    {

        /** @var Order $order */
        $order = $this->getById($order_id);

        if (!$order instanceof Order) {
            throw new \Exception("Order not found");
        }

        $order->setCoordinator($user);
        $order->setReservedAt(new DateTime());
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }



    public function unassignOrderCoordinator($order_id)
    {

        /** @var Order $order */
        $order = $this->getById($order_id);

        if (!$order instanceof Order) {
            throw new \Exception("Order not found");
        }

        $order->setCoordinator(null);
        $order->setReservedAt(null);
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
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
            $dbCourier = $order->getCourier();
            if ($dbCourier instanceof Volunteer) {
                $dbCourier->removeDeliveredOrder($order);
            }
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



    public function findAllNewInTownAvailable($town, $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->where("o.stat = 'new'")
            ->setParameter('town', $town)
            ->andWhere("a.city = :town")
            ->setParameter('user', $user->getId())
            ->andWhere("o.coordinator = :user OR o.coordinator is NULL");
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
        $em = $this->getEntityManager();

        /** @var Order $order */
        $order = $this->findOneBy(['id' => $orderId]);

        /** @var Volunteer $courier */
        $courier = $order->getCourier();
        if ($courier instanceof Volunteer) {
            $courier->removeDeliveredOrder($order);
            $em->persist($courier);
        } else {
            $order->setCourier(null);
            $em->persist($order);
        }
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



    public function getByTown(string $town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.deliveryAddress', 'a')
            ->setParameter('town', $town)
            ->andWhere("a.city = :town");
        $query = $qb->getQuery();

        return $query->getResult();
    }



    public function getAllUnprocessed()
    {
        return $this->findBy(['stat' => 'new']);
    }



    public function getUnprocessedByTown($town)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from('\SousedskaPomoc\Entities\Order', 'o')
            ->leftJoin('o.pickupAddress', 'a')
            ->setParameter('town', $town)
            ->setParameter('stat', 'new')
            ->andWhere("a.city = :town")
            ->andWhere("o.stat = :stat");
        $query = $qb->getQuery();

        return $query->getResult();
    }



    public function getAllForGrid()
    {
        $dataset = [];

        /** @var Order $order */
        foreach ($this->findBy([], ['id' => 'DESC']) as $order) {
            if ($order->getDeliveryAddress() !== null) {
                /** @var Address $city */
                $city = $order->getDeliveryAddress()->getFullAddress();
            }

            $dataset[] = [
                'id' => $order->getId(),
                'owner' => $order->getOwner()->getPersonName(),
                'delivery_address' => $city,
                'delivery_phone' => $order->getDeliveryPhone(),
                'items' => $order->getItems(),
                'createdAt' => $order->getCreatedAt(),
                'status' => $order->getStatus(),
            ];

            $city = null;
        }

        return $dataset;
    }



    public function getAllOldReservedForGrid()
    {
        $dataset = [];

        /** @var Order $order */
        foreach ($this->findBy([
            'stat' => 'new',
        ], ['id' => 'DESC']) as $order) {
            if ($order->getReservedAt() == null) {
                continue;
            }

            /** @var DateTime $date */
            $date = $order->getReservedAt();
            $date->modify('+30 minutes');
            $now = new DateTime();
            if ($now < $date) {
                continue;
            }

            if ($order->getDeliveryAddress() !== null) {
                /** @var Address $city */
                $city = $order->getDeliveryAddress()->getFullAddress();
            }

            $dataset[] = [
                'id' => $order->getId(),
                'owner' => $order->getOwner()->getPersonName(),
                'delivery_address' => $city,
                'delivery_phone' => $order->getDeliveryPhone(),
                'items' => $order->getItems(),
                'createdAt' => $order->getCreatedAt(),
                'reservedAt' => $order->getReservedAt(),
                'coordinatorName' => $order->getCoordinator()->getPersonName(),
                'coordinatorPhone' => $order->getCoordinator()->getPersonPhone(),
                'status' => $order->getStatus(),
            ];

            $city = null;
        }

        return $dataset;
    }
}

<?php

namespace SousedskaPomoc\Model;

use Nette;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

final class OrderManager
{
    use Nette\SmartObject;

    /** @var \Nette\Database\Context */
    protected $database;

    /** @var  \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;



    /**
     * OrderManager constructor.
     *
     * @param \Nette\Database\Context $database
     */
    public function __construct(
        Nette\Database\Context $database,
        OrderRepository $orderRepository,
        VolunteerRepository $volunteerRepository
    ) {
        $this->database = $database;
        $this->orderRepository = $orderRepository;
        $this->volunteerRepository = $volunteerRepository;
    }



    /**
     * @param $values
     *
     * @return bool|int|\Nette\Database\Table\ActiveRow
     */
    public function create($values)
    {
        return $this->orderRepository->create($values);
    }



    /**
     * @param $userId
     *
     * @return array|\Nette\Database\Table\IRow[]
     */
    public function findAllForUser($userId)
    {
        return $this->orderRepository->findAllForUser($userId);
    }



    public function findAllForCourier($userId)
    {
        return $this->orderRepository->findAllForCourier($userId);
    }



    /**
     * @param $id
     *
     * @return \Nette\Database\IRow|\Nette\Database\Table\ActiveRow|null
     */
    public function find($id)
    {
        return $this->database->table('posted_orders')->wherePrimary($id)->fetch();
    }



    public function findAllNew()
    {
        return $this->orderRepository->findAllNew();
    }



    public function changeStatus($orderId, $status)
    {
        $this->orderRepository->changeStatus($orderId, $status);
    }



    public function updateNote($orderId, $note)
    {
        $this->orderRepository->updateCourierNote($orderId, $note);
    }



    public function findAllLive()
    {
        return $this->orderRepository->findAllLive();
    }



    public function findAllLiveByCourierByTown($town, $userId)
    {
        return $this->orderRepository->findAllLiveByCourierByTown($town, $userId);
    }



    public function findAllDelivered()
    {
        return $this->orderRepository->findAllDelivered();
    }



    public function assignOrder($courier_id, $order_id, $operator_id, $status = "assigned")
    {
        $courier = $this->volunteerRepository->getById($courier_id);
        $this->orderRepository->assignOrder($courier, $order_id);
    }



    public function assignOrderCoordinator($order_id, $operator_id)
    {
        $user = $this->volunteerRepository->getById($operator_id);

        $this->orderRepository->assignOrderCoordinator($order_id, $user);
    }



    public function unassignOrderCoordinator($order_id)
    {
        $this->orderRepository->unassignOrderCoordinator($order_id);
    }



    public function updateStatus($orderId, $orderStatus = null)
    {
        $this->orderRepository->updateStatus($orderId, $orderStatus);
    }



    public function fetchCount()
    {
        return $this->orderRepository->fetchCount();
    }



    public function findAllNewInTown($town)
    {
        return $this->orderRepository->findAllNewInTown($town);
    }



    public function findAllLiveInTown($town, $operatorId)
    {
        return $this->orderRepository->findAllLiveInTown($town);
    }



    public function findAllDeliveredInTown($town, $operatorId)
    {
        return $this->orderRepository->findAllDeliveredInTown($town);
    }



    public function saveDemand($demand)
    {
        $volunteerPlaceholder = [
            'personName' => 'poptávka z webu',
            'personPhone' => 0,
            'personEmail' => 'info@sousedskapomoc.cz',
            'town' => $demand->address,
        ];

        $data = $this->database->table("volunteers")->insert($volunteerPlaceholder);

        $output = [
            'id_volunteers' => $data->id,
            'status' => 'waiting',
            'delivery_address' => $demand->deliveryAddress ?? 'neznámá adresa',
            'delivery_phone' => $demand->deliveryPhone,
            'note' => "[Z WEBU] Poptávka pro: ".$demand->deliveryPerson,
            'order_items' => $demand->orderItems,
        ];

        $this->database->table("posted_orders")->insert($output);
    }



    public function fetchAllWebDemands()
    {
        $sql = "SELECT * FROM posted_orders WHERE status = 'waiting'";

        return $this->database->query($sql)->fetchAll();
    }



    public function findAll()
    {
        return $this->orderRepository->findAll();
    }



    public function removeOperator($orderId)
    {
        $sql = "UPDATE posted_orders SET operator_id = null WHERE id = $orderId";

        return $this->database->query($sql);
    }



    public function removeCourier($orderId)
    {
        return $this->orderRepository->removeCourier($orderId);
    }



    public function findAllOrdersData()
    {
        $sql = "SELECT * FROM dispatch_orders_by_town";

        return $this->database->query($sql)->fetchAll();
    }



    public function remove($id)
    {
        $this->orderRepository->remove($id);
    }



    public function updateTown($orderId, $town)
    {
        if ($town != null) {
            $this->database->table('posted_orders')->wherePrimary($orderId)->update([
                'town' => $town,
            ]);
        }
    }



    public function fetchDeliveredCount()
    {
        return $this->orderRepository->fetchDeliveredCount();
    }
}

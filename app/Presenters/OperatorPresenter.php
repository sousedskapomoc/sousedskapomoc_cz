<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\OrderRepository;

final class OperatorPresenter extends BasePresenter
{
    /** @var OrderManager */
    protected $orderManager;

    /** @var OrderRepository */
    protected $orderRepository;



    public function beforeRender()
    {
        parent::beforeRender(); // TODO: Change the autogenerated stub

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }



    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }



    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }



    public function renderDashboard()
    {
        $this->template->newOrders = $this->orderManager->findAllNewInTown($this->template->town);
        $this->template->liveOrders = $this->orderManager->findAllLiveInTown(
            $this->template->town,
            $this->user->getId()
        );
        $this->template->deliveredOrders = $this->orderManager->findAllDeliveredInTown(
            $this->template->town,
            $this->user->getId()
        );
    }



    public function handleUpdateOrderStatus($orderId, $orderStatus)
    {
        $orderStatus = $_POST['orderStatus'] ?? $orderStatus;
        $this->orderManager->updateStatus($orderId, $orderStatus);
        $this->flashMessage($this->translator->translate('messages.order.statusChanged'));
        $this->redirect('this');
    }



    public function handleUnassignOrder($orderId)
    {
        $this->orderManager->removeCourier($orderId);
        $this->orderManager->updateStatus($orderId, 'new');
        $this->redirect('this');
    }



    public function handleAssignCourier()
    {
        $this->orderManager->assignOrder(
            $_POST['courier_id'],
            $_POST['order_id'],
            $this->user->getId()
        );

        $this->flashMessage($this->translator->translate('messages.order.givenToCourier'));
        $this->redirect('this');
    }



    public function handleAssignCoordinator($orderId)
    {
        $this->orderManager->assignOrderCoordinator($orderId, $this->user->getId());

        $this->flashMessage($this->translator->translate('messages.order.reserved'));
        $this->redirect('this');
    }



    public function handleUnassignCoordinator($orderId)
    {
        $this->orderManager->unassignOrderCoordinator($orderId);

        $this->flashMessage($this->translator->translate('messages.order.unreserved'));
        $this->redirect('this');
    }
}

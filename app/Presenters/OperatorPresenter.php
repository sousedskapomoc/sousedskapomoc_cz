<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;


use SousedskaPomoc\Repository\OrderRepository;

final class OperatorPresenter extends BasePresenter
{

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    public function injectOrderRepository(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function renderDashboard()
    {
        $this->template->newOrders = $this->orderRepository->getAllInTownByStatus($this->user->getIdentity()->data['city'], 'new');
        $this->template->liveOrders = $this->orderRepository->getAllLiveInTown($this->user->getIdentity()->data['city']);
        $this->template->deliveredOrders = $this->orderRepository->getAllInTownByStatus($this->user->getIdentity()->data['city'], 'delivered');
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
		$this->orderManager->removeOperator($orderId);
		$this->orderManager->updateStatus($orderId, 'new');
		$this->redirect('this');
	}

    public function handleAssignCourier()
    {
        $user = $this->volunteerRepository->getById($_POST['courier_id']);
        $this->orderRepository->assignOrder($_POST['order_id'], $user);
        $this->flashMessage($this->translator->translate('messages.order.givenToCourier'));
        $this->redirect('this');
    }
}

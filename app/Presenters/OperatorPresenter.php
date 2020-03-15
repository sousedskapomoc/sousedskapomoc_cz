<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Model\OrderManager;

final class OperatorPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Model\OrderManager */
    protected $orderManager;



    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }



    public function renderDashboard()
    {
        $this->template->newOrders = $this->orderManager->findAllNew();
        $this->template->liveOrders = $this->orderManager->findAllLive();
        $this->template->deliveredOrders = $this->orderManager->findAllDelivered();
    }



    public function handleAssignCourier()
    {
        $this->orderManager->assignOrder($_POST['courier_id'], $_POST['order_id']);
        $this->flashMessage("Objednávka byla přiřazena kurýrovi");
        $this->redirect('this');
    }
}
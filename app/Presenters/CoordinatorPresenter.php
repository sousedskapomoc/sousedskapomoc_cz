<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\ComponentModel\IComponent;
use SousedskaPomoc\Components\ICreateOrderFormInterface;
use SousedskaPomoc\Components\IEditOrderFormInterface;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\OrderRepository;

final class CoordinatorPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Model\OrderManager */
    protected $orderManager;

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    /** @var \SousedskaPomoc\Components\ICreateOrderFormInterface */
    protected $orderFormFactory;

    /** @var \SousedskaPomoc\Components\IEditOrderFormInterface */
    protected $editOrderFormFactory;

    protected $orderId;

    public function beforeRender()
    {
        parent::beforeRender(); // TODO: Change the autogenerated stub

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    public function injectOrderFormFactory(ICreateOrderFormInterface $orderFormFactory)
    {
        $this->orderFormFactory = $orderFormFactory;
    }

    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public function injectEditOrderFormFactory(IEditOrderFormInterface $editOrderForm)
    {
        $this->editOrderFormFactory = $editOrderForm;
    }

    public function createComponentEditOrderForm()
    {
        return $this->editOrderFormFactory->create();
    }

    public function renderPrintMaterial($id)
    {
        $this->template->id = $id;
    }


    public function renderDashboard()
    {
        $this->template->orders = $this->orderManager->findAllForUser($this->user->getId());
    }


    public function renderDetail($id)
    {
        $this->template->order = $this->orderRepository->getById($id);
    }


    public function createComponentPostOrder()
    {
        return $this->orderFormFactory->create();
    }


    public function postOrder(BootstrapForm $form)
    {
        $values = $form->getValues();

        $values->town = $this->userManager->getTownForUser($this->user->getId());
        $values->id_volunteers = $this->user->getId();
        $values->status = "new";

        $result = $this->orderManager->create($values);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Coordinator:dashboard");
    }
}

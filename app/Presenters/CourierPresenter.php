<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\OrderRepository;

final class CourierPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Model\OrderManager */
    protected $orderManager;

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    protected $orderId;

    public function beforeRender()
    {
        parent::beforeRender();

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }


    public function createComponentEditOrder()
    {
        $form = new BootstrapForm();
        $form->addHidden('id')
            ->setDefaultValue($this->orderId);
        $form->addTextArea('courier_note', $this->translator->translate('forms.postOrder.courierNote'));
        $form->addSubmit('postOrderFormSubmit', $this->translator->translate('forms.postOrder.buttonCourierNote'));
        $form->onSuccess[] = [$this, "postOrder"];

        return $form;
    }


    public function postOrder(BootstrapForm $form)
    {
        $values = $form->getValues();

        $result = $this->orderRepository->updateCourierNote($values->id, $values->courier_note);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Courier:dashboard");
    }


    public function renderDashboard()
    {
        $this->template->userOnline = $this->userManager->isOnline($this->user->getId());
        $this->template->orders = $this->orderManager->findAllLiveByCourierByTown(
            $this->template->town,
            $this->user->getId()
        );
    }


    public function renderDetail($id)
    {
        $this->template->order = $this->orderRepository->getById($id);
    }


    public function handleToggleActive($active)
    {
        $this->userManager->setOnline($this->user->getId(), $active);
        $this->flashMessage( $this->translator->translate('messages.statusChange.changeSuccess') );
        $this->redirect('this');
    }


    public function handleChangeStatus($id, $status)
    {
        $this->orderRepository->changeStatus($id, $status);
        $this->flashMessage( $this->translator->translate('messages.statusChange.changeSuccess') );
        $this->redirect('this');
    }


    public function actionEdit($id)
    {

        $this->orderId = $id;
    }
}

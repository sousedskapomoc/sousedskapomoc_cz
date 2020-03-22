<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Entities\Orders;

final class CourierPresenter extends BasePresenter
{

    protected $orderId;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

	public function beforeRender()
	{
		parent::beforeRender();

		if (!$this->user->isLoggedIn()) {
			$this->redirect('Homepage:default');
		}
	}

    public function injectOrderRepository(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }



    public function createComponentEditOrder()
    {
        $form = new BootstrapForm();
        $form->addHidden('id')
            ->setDefaultValue($this->orderId);
        $form->addTextArea('courier_note', $this->translator->translate('forms.postOrder.courierNote'));
        $form->addSubmit('postOrderFormSubmit', $this->translator->translate('forms.postOrder.buttonCourierNote'));
        $form->onSuccess[] = [$this, "updateOrder"];

        return $form;
    }



    public function updateOrder(BootstrapForm $form)
    {
        $values = $form->getValues();
        $this->orderRepository->updateCourierNote($values->id, $values->courier_note);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Coordinator:dashboard");
    }


	public function renderDashboard()
	{
		$user = $this->volunteerRepository->isOnline($this->user->getId());
		$this->template->userOnline = $user->active;
		$this->template->orders = $this->orderRepository->findAllLiveByCourierByTown(
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
		$this->volunteerRepository->setOnline($this->user->getId(), $active);
		$this->flashMessage("Změna stavu byla nastavena.");
		$this->redirect('this');
	}


	public function handleChangeStatus($id, $status)
	{
		$this->orderRepository->changeStatus($id, $status);
		$this->flashMessage("Změna stavu byla nastavena.");
		$this->redirect('this');
	}


	public function actionEdit($id)
	{
		$this->orderId = $id;
	}
}

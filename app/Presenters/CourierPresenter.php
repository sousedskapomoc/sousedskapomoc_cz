<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use SousedskaPomoc\Model\OrderManager;

final class CourierPresenter extends BasePresenter
{
	/** @var \SousedskaPomoc\Model\OrderManager */
	protected $orderManager;

	protected $orderId;

	public function beforeRender()
	{
		parent::beforeRender();

		if (!$this->user->isLoggedIn()) {
			$this->redirect('Homepage:default');
		}
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

		$result = $this->orderManager->updateNote($values->id, $values->courier_note);
		$this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
		$this->redirect("Coordinator:dashboard");
	}


	public function renderDashboard()
	{
		$user = $this->userManager->isOnline($this->user->getId());
		$this->template->userOnline = $user->active;
		$this->template->orders = $this->orderManager->findAllLiveByCourierByTown(
			$this->template->town,
			$this->user->getId()
		);
	}


	public function renderDetail($id)
	{
		$this->template->order = $this->orderManager->find($id);
	}


	public function handleToggleActive($active)
	{
		$this->userManager->setOnline($this->user->getId(), $active);
		$this->flashMessage("Změna stavu byla nastavena.");
		$this->redirect('this');
	}


	public function handleChangeStatus($id, $status)
	{
		$this->orderManager->changeStatus($id, $status);
		$this->flashMessage("Změna stavu byla nastavena.");
		$this->redirect('this');
	}


	public function actionEdit($id)
	{

		$this->orderId = $id;
	}
}

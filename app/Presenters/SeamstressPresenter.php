<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use SousedskaPomoc\Entities\Orders;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\OrderRepository;

final class SeamstressPresenter extends BasePresenter
{

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    public function injectOrderRepository(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function createComponentPostOrder()
    {
        $form = new BootstrapForm();
        $form->addText('delivery_address', $this->translator->translate('forms.postOrder.addressPick'))
            ->setRequired($this->translator->translate('forms.postOrder.addressRequired'))
            ->setPlaceholder($this->translator->translate('forms.postOrder.addressPlaceholder'));
//        $form->addText('delivery_phone', $this->translator->translate('forms.postOrder.phoneLabel'))
//            ->setPlaceholder($this->translator->translate('forms.postOrder.phonePlaceholder'));
        $form->addHidden('note')
            ->setDefaultValue('rousky');
        $form->addText('order_items', $this->translator->translate('templates.seamstress.itemsLabel'))
            ->setHtmlAttribute('rows', 10);
        $form->addSubmit('postOrderFormSubmit', $this->translator->translate('templates.seamstress.button'));
        $form->onSuccess[] = [$this, "postOrder"];

        return $form;
    }



    public function postOrder(BootstrapForm $form)
    {
        $values = $form->getValues();

		$values->id_volunteers = $this->user->getId();
		$values->delivery_phone = $this->user->getIdentity()->data['personPhone'] ?? 'neuveden';
		$values->status = "new";

        $order = new Orders();
        $order->setStatus('new');
        $order->setAuthor($this->user->getId());
        $order->setPickupAddress($values->delivery_address);
        $order->setItems($values->order_items);
        $order->setCustomerNote($values->note);


        $result = $this->orderRepository->create($order);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Coordinator:dashboard");
    }



    public function renderDashboard()
    {
        $this->template->orders = $this->orderRepository->getByUser($this->user->getId());
        $this->template->userOnline = $this->volunteerRepository->isOnline($this->user->getId());;
    }



    public function handleToggleActive($active)
    {
        $this->volunteerRepository->setOnline($this->user->getId(), $active);
        $this->flashMessage("Změna stavu byla nastavena.");
        $this->redirect('this');
    }
}

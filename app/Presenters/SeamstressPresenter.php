<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use SousedskaPomoc\Model\OrderManager;

final class SeamstressPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Model\OrderManager */
    protected $orderManager;



    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }



    public function createComponentPostOrder()
    {
        $form = new BootstrapForm();
        $form->addText('delivery_address', $this->translator->translate('forms.postOrder.addressLabel'))
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
        $values->status = "new";

        $result = $this->orderManager->create($values);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Coordinator:dashboard");
    }



    public function handleToggleActive($active)
    {
        $this->template->userActive = $active;
    }



    public function renderDashboard()
    {
        $this->template->orders = $this->orderManager->findAllForUser($this->user->getId());
    }
}

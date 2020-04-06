<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use SousedskaPomoc\Model\OrderManager;

final class SeamstressPresenter extends BasePresenter
{
    /** @var OrderManager */
    protected $orderManager;

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


    public function createComponentPostOrder()
    {
        $form = new BootstrapForm();

        $form->addText('delivery_address', $this->translator->translate('forms.postOrder.addressPick'))
            ->setRequired($this->translator->translate('forms.postOrder.addressRequired'))
            ->setPlaceholder($this->translator->translate('forms.postOrder.addressPlaceholder'));

        $form->addHidden('note')->setDefaultValue('rousky');

        $form->addText('order_items', $this->translator->translate('templates.seamstress.itemsLabel'))
            ->setPlaceholder('min. 10 kusů')
            ->setRequired('Zadejte prosím počet roušek k vyzvednutí');

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

        $result = $this->orderManager->create($values);
        $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
        $this->redirect("Coordinator:dashboard");
    }


    public function renderDashboard()
    {
        $this->template->orders = $this->orderManager->findAllForUser($this->user->getId());
        $this->template->userOnline = $this->userManager->isOnline($this->user->getId());
    }

    public function handleToggleActive($active)
    {
        $this->userManager->setOnline($this->user->getId(), $active);
        $this->flashMessage("Změna stavu byla nastavena.");
        $this->redirect('this');
    }
}

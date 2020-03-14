<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use SousedskaPomoc\Model\OrderManager;

final class CoordinatorPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Model\OrderManager */
    protected $orderManager;



    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }



    public function renderPrintMaterial($id)
    {
        $this->template->id = $id;
    }

    public function renderDashboard() {
        $this->template->orders = $this->orderManager->findAllForUser($this->user->getId());
    }

    public function renderDetail($id) {
        $this->template->order = $this->orderManager->find($id);
    }



    public function createComponentPostOrder()
    {
        $form = new BootstrapForm();
        $form->addText('delivery_address',
            'Adresa kam dovezeme nákup')->setRequired('Potřebujeme vědět kam máme nákup odvézt')->setPlaceholder("např. Palackého náměstí 1, Kolín");
        $form->addText('note',
            'poznámka k nákupu (např. pro koho je určen pro snažší odlišení)')->setPlaceholder("pro paní Novákovou");
        $form->addTextArea('order_items', 'Položky objednávky (oddělené ENTERem)')->setHtmlAttribute('rows', 10);
        $form->addSubmit('postOrderFormSubmit', 'Vložit objednávku do systému');
        $form->onSuccess[] = [$this, "postOrder"];

        return $form;
    }



    public function postOrder(BootstrapForm $form)
    {
        $values = $form->getValues();

        $values->id_volunteers = $this->user->getId();
        $values->status = "new";

        $result = $this->orderManager->create($values);
        $this->flashMessage("Vaše objednávka byla uložena.");
        $this->redirect("Coordinator:dashboard");
    }
}
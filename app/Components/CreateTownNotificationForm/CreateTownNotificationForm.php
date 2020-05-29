<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Control;

class CreateTownNotificationForm extends Control
{
    public function createComponentForm()
    {
        $form = new BootstrapForm();
        $form->addText("title", "Název notifikace");
        $form->addDateTime("date", "Datum");
        $form->addTextArea("perex", "Anotace(zobrazované dálka dle framu v náhledu)");
        $form->addTextArea("description", "Hlavní tělo notifikace(přístupné po rozkliku)");
        $form->addText("townPart", "Místní příslušnost(pouze část města / celé město)");
        $form->addText("externalLink", "Link na externí stránku události(možnost uvést FB nebo klasickou stránku)");
        $form->addRadioList('publication', 'Notifikovat', ['pouze na webu','e-mailem','SMS zprávou']);
        $form->addSubmit("notificationFormSubmit", "Uložit notifikaci");
        return $form;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/form.latte');
        $this->template->render();
    }
}

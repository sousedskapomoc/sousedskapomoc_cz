<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Control;

class CreateTownEventForm extends Control
{
    protected $townList = [];

    public function createComponentForm()
    {
        $form = new BootstrapForm;

        $form->addGroup('Obecné informace');
        $form->addSelect('town', 'Město které pořádá událost', $this->townList);
        $form->addText('title', 'Název události');
        $form->addTextArea('description', 'Popis události');
        $form->addGroup('Datum a počet dobrovolníků');
        $form->addDateTime('dateOfEvent', 'Datum události');
        $form->addText('volunteersLimit', 'Kolik dobrovolníků je třeba');
        $form->addGroup('Další');
        $form->addCheckbox('visible', 'Veřejně dostupná událost');
        $form->addRadioList('publication', 'Zveřejnit', ['okamžitě','v den konání události','až budu chtít']);

        $form->addSubmit('createTownEventSubmit', 'Založit událost');
        $form->onSuccess[] = [$this, "processForm"];
        return $form;
    }

    public function processForm(BootstrapForm $form)
    {
        $data = $form->getValues();
        \Tracy\Debugger::dump($data);
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . "/form.latte");
        $this->template->render();
    }
}

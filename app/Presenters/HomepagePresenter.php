<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;


use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Database\Connection;
use SousedskaPomoc\Components\Mail;
use SousedskaPomoc\Model\UserManager;

final class HomepagePresenter extends BasePresenter
{
    /** @var \Nette\Database\Connection */
    protected $connection;

    /** @var \SousedskaPomoc\Model\UserManager */
    protected $userManager;

    /** @var \SousedskaPomoc\Components\Mail */
    protected $mail;



    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect("System:dashboard");
        }
    }



    public function injectUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }



    public function injectMail(Mail $mail)
    {
        $this->mail = $mail;
    }



    public function injectConnection(Connection $connection)
    {
        $this->connection = $connection;
    }



    public function createComponentRegisterAsCoordinator()
    {
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'coordinator');

        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        $form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

        $form->addSubmit('coordinatorRegFormSubmit', $this->translator->translate('forms.registerCoordinator.button'));
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }



    public function createComponentRegisterAsOperator()
    {
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'operator');

        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        $form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

        $form->addSubmit('coordinatorRegFormSubmit', $this->translator->translate('forms.registerCoordinator.button'));
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }



    public function createComponentRegisterAsCourier()
    {
        $cars = [
            1 => $this->translator->translate('forms.cars.small'),
            2 => $this->translator->translate('forms.cars.big'),
            3 => $this->translator->translate('forms.cars.smallTruck'),
            4 => $this->translator->translate('forms.cars.bigTruck'),
            5 => $this->translator->translate('forms.cars.bike'),
            6 => $this->translator->translate('forms.cars.walk'),
        ];

        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'courier');

        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        $form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

        $form->addSelect('car', $this->translator->translate('forms.registerCoordinator.carLabel'), $cars)
            ->setRequired($this->translator->translate('forms.registerCoordinator.carRequired'));

        $form->addSubmit('coordinatorRegFormSubmit', $this->translator->translate('forms.registerCoordinator.button'));
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }



    public function processRegistration(BootstrapForm $form)
    {
        $values = $form->getValues();
        if (!$this->userManager->check('personEmail', $values->personEmail)) {

            $this->userManager->register($values);

            $this->flashMessage($this->translator->translate('messages.registration.success'));
            $this->redirect("RegistrationFinished");
        } else {
            $form->addError($this->translator->translate('messages.registration.fail'));
        }
    }
}

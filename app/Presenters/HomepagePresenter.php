<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;


use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
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

    protected $emailCode;

    protected $passwords;



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



    public function injectPassword(Passwords $passwords)
    {
        $this->passwords = $passwords;
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



    public function createComponentChangePasswordForm()
    {
        $form = new BootstrapForm;

        $user = $this->userManager->getUserByEmailCode($this->emailCode);
        $form->addHidden('personEmail');
        $form->addHidden('id');

        if (isset($user['id']) && isset($user['personEmail'])) {
            $form->setDefaults(['personEmail' => $user['personEmail'], 'id' => $user['id']]);
        }

        $form->addPassword("newPass", "Nové heslo")
            ->addRule(Form::MIN_LENGTH, 'Heslo musi byt alespon %d dlouhe', 6)
            ->setRequired('Prosím zvolte si heslo.');

        $form->addPassword("newPassAgain", "Zopakujte nové heslo")
            ->setRequired('Passwords must be the same')
            ->addRule(Form::MIN_LENGTH, 'Heslo musi byt alespon %d dlouhe', 6)
            ->addRule(FORM::EQUAL, "Hesla se neshodují.", $form["newPass"]);

        $form->addSubmit('submit', 'Nastavit heslo');
        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }



    public function onSuccess(Form $form, $values)
    {
        if (!$this->userManager->check('personEmail', $values->personEmail)) {
            $form->addError('Zadaný účet neexistuje.');
        }

        try {
            $pass = $this->passwords->hash($values->newPass);
            $this->userManager->setPass($values->id, $pass);
            $this->presenter->flashMessage("Heslo bylo úspěšně změněno.");
            $this->presenter->redirect("Sign:in");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
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



    public function actionChangePassword()
    {
        $this->emailCode = $this->presenter->getParameter('hash');
        try {
            $this->userManager->getUserByEmailCode($this->emailCode);
        } catch (\Exception $err) {
            $this->flashMessage("Email code is not valid.", BasePresenter::FLASH_TYPE_ERROR);
            $this->redirect("Page:homepage");
        }
    }

}

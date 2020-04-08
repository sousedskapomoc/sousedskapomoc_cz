<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Database\Connection;
use SousedskaPomoc\Components\IRegisterVolunteerFormInterface;
use SousedskaPomoc\Components\Mail;
use SousedskaPomoc\Model\UserManager;
use SousedskaPomoc\Repository\RoleRepository;

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

    protected $personEmail;

    protected $id;

    /** @var \SousedskaPomoc\Components\IRegisterVolunteerFormInterface */
    protected $registerVolunteerForm;

    /** @var \SousedskaPomoc\Repository\RoleRepository */
    protected $roleRepository;


    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect("System:dashboard");
        }
    }

    public function injectRoleRepository(RoleRepository $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    public function injectRegisterVolunteerFormFactory(IRegisterVolunteerFormInterface $registerVolunteerForm) {
        $this->registerVolunteerForm = $registerVolunteerForm;
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
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('coordinator');
        return $this->registerVolunteerForm->create($role);
    }

    public function createComponentRegisterAsMedicalCoordinator()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('medicalCoordinator');
        return $this->registerVolunteerForm->create($role);
    }


    public function createComponentRegisterAsSeamstress()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('seamstress');
        return $this->registerVolunteerForm->create($role);
    }


    public function createComponentRegisterAsOperator()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('operator');
        return $this->registerVolunteerForm->create($role);
    }

    public function createComponentPostDemand()
    {
        $form = new BootstrapForm;
        $form->addText('address', 'Město ve kterém jste');
        $form->addText('deliveryAddress', 'Adresa doručení');
        $form->addText('deliveryPhone', 'Telefon');
        $form->addText('deliveryPerson', 'Jméno a příjmení');
        $form->addTextArea('orderItems', 'Položky objednávky');
        $form->addSubmit('demandFormSubmit', 'Uložit poptávku');
        $form->onSuccess[] = [$this, "saveDemand"];
        return $form;
    }

    public function saveDemand(BootstrapForm $form)
    {
        $values = $form->getValues();
        $this->orderManager->saveDemand($values);
        $this->flashMessage("Uložili jsme co potřebujete a pracujeme na tom ať to odbavíme");
        $this->redirect('Homepage:default');
    }

    public function createComponentAddGovernmentCoordinator()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('superuser');
        return $this->registerVolunteerForm->create($role);
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
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('courier');
        return $this->registerVolunteerForm->create($role);
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


    public function actionChangePassword($hash = null)
    {
        $this->emailCode = $this->presenter->getParameter('hash');
        try {
            $user = $this->userManager->getUserByEmailCode($this->emailCode);
        } catch (\Exception $err) {
            $this->flashMessage("Email code is not valid.", BasePresenter::FLASH_TYPE_ERROR);
            $this->redirect("Page:homepage");
        }
    }


    public function createComponentLostPasswordForm()
    {
        $form = new BootstrapForm;

        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        $form->addSubmit('submit', 'Zaslat nove heslo');
        $form->onSuccess[] = [$this, 'onOk'];

        return $form;
    }


    public function onOk(Form $form, $values)
    {
        if (!$this->userManager->check('personEmail', $values->personEmail)) {
            $form->addError('Zadaný účet neexistuje.');
        }

        try {
            $hash = $this->userManager->getUserByEmail($values->personEmail)->emailCode;
            $link = $this->lemink('//Homepage:changePassword', $hash);
            $this->mail->sendLostPasswordMail($values->personEmail, $link);
            $this->presenter->flashMessage("E-mail s odkazem byl úspěšně odeslán.");
            $this->presenter->redirect("Sign:in");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

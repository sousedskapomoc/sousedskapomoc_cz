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
use SousedskaPomoc\Components\IDemandFormInterface;

final class HomepagePresenter extends BasePresenter
{
    /** @var \Nette\Database\Connection */
    protected $connection;

    /** @var \SousedskaPomoc\Model\UserManager */
    protected $userManager;

    /** @var \SousedskaPomoc\Components\Mail */
    protected $mail;

    /** @var IDemandFormInterface */
    protected $demandFormFactory;

    protected $emailCode;

    protected $passwords;

    protected $personEmail;

    protected $id;

    /** @var \SousedskaPomoc\Components\IRegisterVolunteerFormInterface */
    protected $registerVolunteerForm;

    /** @var \SousedskaPomoc\Repository\RoleRepository */
    protected $roleRepository;


    public function injectRoleRepository(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function injectDemandFormFactory(IDemandFormInterface $demandForm)
    {
        $this->demandFormFactory = $demandForm;
    }

    public function injectRegisterVolunteerFormFactory(IRegisterVolunteerFormInterface $registerVolunteerForm)
    {
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
        return $this->demandFormFactory->create();
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

        $form->addPassword("newPass", $this->translator->translate('forms.changePasswdForm.newPasswd') )
            ->addRule(Form::MIN_LENGTH, $this->translator->translate('forms.changePasswdForm.mustBeAtLeastDChars'), 6)
            ->setRequired($this->translator->translate('forms.changePasswdForm.pickNewPasswd'));

        $form->addPassword("newPassAgain", $this->translator->translate('forms.changePasswdForm.newPasswdAgain'))
            ->setRequired($this->translator->translate('forms.changePasswdForm.passwdMustMatch'))
            ->addRule(Form::MIN_LENGTH, $this->translator->translate('forms.changePasswdForm.mustBeAtLeastDChars'), 6)
            ->addRule(FORM::EQUAL, $this->translator->translate('forms.changePasswdForm.passwdDontMatch'), $form["newPass"]);

        $form->addSubmit('submit', $this->translator->translate('forms.changePasswdForm.setPasswd'));
        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Form $form, $values)
    {
        if (!$this->userManager->check('personEmail', $values->personEmail)) {
            $form->addError($this->translator->translate('forms.onSuccess.accDontExist'));
        }

        try {
            $pass = $this->passwords->hash($values->newPass);
            $this->userManager->setPass($values->id, $pass);
            $this->presenter->flashMessage($this->translator->translate('messages.passwdChange.success'));
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
        $hash = $this->presenter->getParameter('hash');
        try {
            $this->userManager->getUserByEmailCode($hash);
        } catch (\Exception $err) {
            $this->flashMessage( $this->translator->translate('messages.passwdChange.hashMissMatch'), BasePresenter::FLASH_TYPE_ERROR);
            $this->redirect("Page:homepage");
        }
    }


    public function createComponentLostPasswordForm()
    {
        $form = new BootstrapForm;

        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        $form->addSubmit('submit', $this->translator->translate('messages.passwdChange.sendNewPasswd'));
        $form->onSuccess[] = [$this, 'onOk'];

        return $form;
    }


    public function onOk(Form $form, $values)
    {
        if (!$this->userManager->check('personEmail', $values->personEmail)) {
            $form->addError($this->translator->translate('forms.onOk.accDontExist'));
        }

        try {
            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            $user = $this->userManager->getUserByEmail($values->personEmail);
            $hash = $user->getHash();
            $link = $this->link('//Homepage:changePassword', $hash);
            $this->mail->sendLostPasswordMail($values->personEmail, $link);
            $this->presenter->flashMessage($this->translator->translate('messages.passwdChange.emailSendSuccess') );
            $this->presenter->redirect("Sign:in");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Database\Connection;
use SousedskaPomoc\Components\IDemandFormInterface;
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

    /** @var \SousedskaPomoc\Components\IDemandFormInterface */
    protected $demandFormFactory;

    protected $emailCode;

    protected $passwords;

    protected $personEmail;

    protected $id;


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

    public function createComponentWantJoin()
    {
        return $this->registerVolunteerForm->create();
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

        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->userManager->getUserByEmailCode($this->emailCode);
        $form->addHidden('personEmail')
            ->setDefaultValue($user->getPersonEmail());
        $form->addHidden('id')
            ->setDefaultValue($user->getId());

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
        $hash = $this->presenter->getParameter('hash');
        try {
            $this->userManager->getUserByEmailCode($hash);
            $this->emailCode = $hash;
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
            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            $user = $this->userManager->getUserByEmail($values->personEmail);
            $hash = $user->getHash();
            $link = $this->link('//Homepage:changePassword', $hash);
            $this->mail->sendLostPasswordMail($values->personEmail, $link);
            $this->presenter->flashMessage("E-mail s odkazem byl úspěšně odeslán.");
            $this->presenter->redirect("Sign:in");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}

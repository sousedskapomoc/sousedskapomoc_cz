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
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

final class HomepagePresenter extends BasePresenter
{
    /** @var \Nette\Database\Connection */
    protected $connection;

    /** @var \SousedskaPomoc\Model\UserManager */
    protected $userManager;

    /** @var \SousedskaPomoc\Components\Mail */
    protected $mail;

    /** @var \SousedskaPomoc\Components\IRegisterVolunteerFormInterface */
    protected $registerVolunteerFormFactory;

    /** @var \SousedskaPomoc\Repository\RoleRepository*/
    protected $roleRepository;

    protected $emailCode;

    protected $passwords;

    protected $personEmail;

    protected $id;


    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;



    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect("System:dashboard");
        }
    }



    public function injectMail(Mail $mail)
    {
        $this->mail = $mail;
    }



    public function injectPassword(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }

    public function injectRegisterVolunteerFactory(IRegisterVolunteerFormInterface $registerVolunteerForm) {
        $this->registerVolunteerFormFactory = $registerVolunteerForm;
    }

    public function injectRoleRepository(RoleRepository $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    public function injectVolunteerRepository(VolunteerRepository $volunteerRepository) {
        $this->volunteerRepository = $volunteerRepository;
    }

    public function createComponentRegisterAsCoordinator()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('coordinator');
        return $this->registerVolunteerFormFactory->create($role);
    }



    public function createComponentRegisterAsSeamstress()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('seamstress');
        return $this->registerVolunteerFormFactory->create($role);
    }



    public function createComponentRegisterAsOperator()
    {
        /** @var \SousedskaPomoc\Entities\Role $role */
        $role = $this->roleRepository->getByName('operator');
        return $this->registerVolunteerFormFactory->create($role);
    }



	public function createComponentChangePasswordForm()
	{
		$form = new BootstrapForm;

        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getUserByHash($this->emailCode);
        $form->addHidden('personEmail');
        $form->addHidden('id');

        $form->setDefaults(['personEmail' => $user->getPersonEmail(), 'id' => $user->getId()]);

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
        /** @var  \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getByEmail($values->personEmail);
        if (!($user instanceof Volunteer)) {
            $form->addError('Zadaný účet neexistuje.');
        }

        try {
            $pass = $this->passwords->hash($values->newPass);
            $this->volunteerRepository->setPass($values->id, $pass);
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
        return $this->registerVolunteerFormFactory->create($role);
    }

    public function actionChangePassword($hash = null)
    {
        $this->emailCode = $this->presenter->getParameter('hash');
        try {
            /** @var \SousedskaPomoc\Entities\Volunteer $user */
            $user = $this->volunteerRepository->getUserByHash($this->emailCode);
        } catch (\Exception $err) {
            $this->flashMessage("Email code is not valid.", 'error');
            $this->redirect("Homepage:default");
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
            $link = $this->link('//Homepage:changePassword', $hash);
            $this->mail->sendLostPasswordMail($values->personEmail, $link);
            $this->presenter->flashMessage("E-mail s odkazem byl úspěšně odeslán.");
            $this->presenter->redirect("Sign:in");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}

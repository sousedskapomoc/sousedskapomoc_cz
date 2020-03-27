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

	protected $personEmail;

	protected $id;


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
		$form->onSuccess[] = [$this, "processRegistrationCoordinator"];

		return $form;
	}

	public function createComponentRegisterAsMedicalCoordinator()
	{
		$form = new BootstrapForm;
		$form->renderMode = RenderMode::VERTICAL_MODE;
		$form->addHidden('role', 'medicalCoordinator');

		$form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
		$form->addText('personPhone', 'Telefon do ordinace')
			->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
		$form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

		$form->addText('town', "Město kde je lékařské zařízení")
			->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

		$form->addSubmit('coordinatorRegFormSubmit', $this->translator->translate('forms.registerCoordinator.button'));
		$form->onSuccess[] = [$this, "processRegistrationMedical"];

		return $form;
	}



	public function createComponentRegisterAsSeamstress()
	{
		$form = new BootstrapForm;
		$form->renderMode = RenderMode::VERTICAL_MODE;
		$form->addHidden('role', 'seamstress');

		$form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
		$form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
		$form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

		$form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
			->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

		$form->addSubmit('coordinatorRegFormSubmit', $this->translator->translate('forms.registerCoordinator.button'));
		$form->onSuccess[] = [$this, "processRegistrationSeamstress"];

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
		$form->onSuccess[] = [$this, "processRegistrationOperator"];

		return $form;
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
		$form = new BootstrapForm;
		$form->addText('address', 'Město ve kterém jste');
		$form->addHidden('role')
			->setDefaultValue('superuser');
		$form->addText('personName', 'Jméno a přijmení');
		$form->addText('personPhone', 'Telefon');
		$form->addText('personEmail', 'E-mail');
		$form->addSubmit('demandFormSubmit', 'Zaregistrovat');
		$form->onSuccess[] = [$this, "addSuper"];
		return $form;
	}

	public function addSuper(BootstrapForm $form)
	{
		$values = $form->getValues();
		$values->town = $values->address;
		unset($values->address);

		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendSuperuserMail($values->personEmail, $link);

			$this->flashMessage("Kontakt uložen. Budeme Vás kontaktovat pro ověření totožnosti.");
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
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
			6 => $this->translator->translate('forms.cars.motorcycle'),
			7 => $this->translator->translate('forms.cars.walk'),
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
		$form->onSuccess[] = [$this, "processRegistrationCourier"];

		return $form;
	}


	public function processRegistrationSeamstress(BootstrapForm $form)
	{
		$values = $form->getValues();
		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendSeamstressMail($values->personEmail, $link);

			$this->flashMessage($this->translator->translate('messages.registration.success'));
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
	}


	public function processRegistrationCourier(BootstrapForm $form)
	{
		$values = $form->getValues();
		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendCourierMail($values->personEmail, $link);

			$this->flashMessage($this->translator->translate('messages.registration.success'));
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
	}


	public function processRegistrationOperator(BootstrapForm $form)
	{
		$values = $form->getValues();
		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendOperatorMail($values->personEmail, $link);

			$this->flashMessage($this->translator->translate('messages.registration.success'));
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
	}


	public function processRegistrationCoordinator(BootstrapForm $form)
	{
		$values = $form->getValues();
		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendCoordinatorMail($values->personEmail, $link);

			$this->flashMessage($this->translator->translate('messages.registration.success'));
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
	}

	public function processRegistrationMedical(BootstrapForm $form)
	{
		$values = $form->getValues();
		if (!$this->userManager->check('personEmail', $values->personEmail)) {

			$user = $this->userManager->register($values);
			$hash = md5($user['personEmail']);
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->userManager->setUserCode($user['id'], $hash);
			$this->mail->sendMedicMail($values->personEmail, $link);

			$this->flashMessage($this->translator->translate('messages.registration.success'));
			$this->redirect("RegistrationFinished");
		} else {
			$form->addError($this->translator->translate('messages.registration.fail'));
		}
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
			$link = $this->link('//Homepage:changePassword', $hash);
			$this->mail->sendLostPasswordMail($values->personEmail, $link);
			$this->presenter->flashMessage("E-mail s odkazem byl úspěšně odeslán.");
			$this->presenter->redirect("Sign:in");
		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}

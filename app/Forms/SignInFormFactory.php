<?php

declare(strict_types=1);

namespace SousedskaPomoc\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use SousedskaPomoc\Repository\VolunteerRepository;
use SousedskaPomoc\Components\Authenticator;


final class SignInFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;

	/** @var VolunteerRepository */
	private $volunteerRepository;

	/** @var Authenticator */
	private $authenticator;


	public function __construct(
	    FormFactory $factory,
        User $user,
        VolunteerRepository $volunteerRepository,
        Authenticator $authenticator
    )
	{
		$this->factory = $factory;
		$this->user = $user;
		$this->volunteerRepository = $volunteerRepository;
		$this->authenticator = $authenticator;
	}


	public function create(callable $onSuccess): Form
	{
		$form = $this->factory->create();

		$form->addText('email', 'Email:')
			->setRequired('Prosím zadejte svůj email');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte své heslo.');

		$form->addCheckbox('remember', 'Trvalé přihlášení');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
				$this->user->login($values->email, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Špatné přihlašovací údaje.');

                return;
            }
			$onSuccess();
		};

		return $form;
	}
}

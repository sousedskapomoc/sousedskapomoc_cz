<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Forms\Form;
use Nette\Security\Passwords;

final class SystemPresenter extends BasePresenter
{
    /** @var \Nette\Security\Passwords */
    protected $passwords;



    public function injectPasswords(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }



    public function beforeRender()
    {
        parent::beforeRender();

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }



    public function renderDashboard()
    {
        $this->template->statistics = [
            'totalCount' => $this->userManager->fetchTotalCount(),
            'couriersCount' => $this->userManager->fetchCountBy(['role' => 'courier']),
            'operatorsCount' => $this->userManager->fetchCountBy(['role' => 'operator']),
            'coordinatorsCount' => $this->userManager->fetchCountBy(['role' => 'coordinator']),
            'seamstressCount' => $this->userManager->fetchCountBy(['role' => 'seamstress']),
            'usersWithoutAccess' => $this->userManager->fetchCountBy(['password' => null]),
            'uniqueTowns' => $this->userManager->fetchUniqueTownsCount(),
            'ordersCount' => $this->orderManager->fetchCount(),
			'deliveredOrdersCount' => $this->orderManager->fetchDeliveredCount(),
        ];
    }

    public function createComponentRegisterAddress() {
    	$form = new BootstrapForm();
    	$form->addText("town","Město ve kterém působím");
    	$form->addHidden("selectedTown")->setRequired("Prosím vyberte z našeptávače město ve kterém působíte.");
		$form->addSubmit("addressSubmit","Uložit adresu");
		$form->onSuccess[] = [$this, "updateAddress"];
    	return $form;
	}

	public function updateAddress(BootstrapForm $form) {
    	$values = $form->getValues();
    	$this->userManager->updateTown($values->selectedTown, $this->user->getId());
    	$this->flashMessage("Adresa byla změněna!",'danger');
    	$this->redirect("System:profile");
	}


    public function createComponentEditForm()
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

        $userDetails = $this->userManager->getUserById($this->user->id);

        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;

        $roles = [
            0 => $this->translator->translate('templates.courier.title'),
            1 => $this->translator->translate('templates.operator.title'),
            2 => $this->translator->translate('templates.seamstress.title'),
            3 => $this->translator->translate('templates.coordinator.title'),
        ];

        $rolesDefault = [];
        if ($this->user->isInRole('courier')) {
            array_push($rolesDefault, 0);
        }
        if ($this->user->isInRole('operator')) {
            array_push($rolesDefault, 1);
        }
        if ($this->user->isInRole('seamstress')) {
            array_push($rolesDefault, 2);
        }
        if ($this->user->isInRole('coordinator')) {
            array_push($rolesDefault, 3);
        }

        $form->addCheckboxList('role', $this->translator->translate('forms.registerCoordinator.role'),
            $roles)
            ->setDefaultValue($rolesDefault);

        $form->addHidden('id');
        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addPassword("password", "Nové heslo");
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        if ($this->user->isInRole('courier')) {
            $form->addSelect('car', $this->translator->translate('forms.registerCoordinator.carLabel'), $cars)
                ->setRequired($this->translator->translate('forms.registerCoordinator.carRequired'))
                ->setDefaultValue($userDetails->car);
        }

        $form->setDefaults(
            [
                'personName' => $userDetails->personName,
                'personEmail' => $userDetails->personEmail,
                'personPhone' => $userDetails->personPhone,
                'town' => $userDetails->town,
                'id' => $userDetails->id,
            ]
        );

        $form->addSubmit('coordinatorEditFormSubmit', $this->translator->translate('templates.profile.button'));
        $form->onSuccess[] = [$this, "processUpdate"];

        return $form;
    }



    public function processUpdate(BootstrapForm $form)
    {
        $values = $form->getValues();
        $finalRoles = '';
        foreach ($values->role as $key => $role) {
            if ($role == 0) {
                $finalRoles = $finalRoles.'courier';
            }
            if ($role == 1) {
                $finalRoles = $finalRoles.'operator';
            }
            if ($role == 2) {
                $finalRoles = $finalRoles.'seamstress';
            }
            if ($role == 3) {
                $finalRoles = $finalRoles.'coordinator';
            }
            if ($key != array_key_last($values->role)) {
                $finalRoles = $finalRoles.';';
            }
        }
        $values->role = $finalRoles;
        if ($values->password == null) {
            unset($values->password);
        } else {
            $values->password = $this->passwords->hash($values->password);
        }
        $usr = $this->userManager->getUserById($values->id);
        if ($usr->id != $values->id) {
            $form->addError($this->translator->translate('templates.profile.fail'));
        } else {
            $user = $this->userManager->update($values);

            $this->flashMessage($this->translator->translate('templates.profile.success'));
            $this->redirect("profile");
        }
    }



    public function renderProfile()
    {
        $this->template->userDetails = $this->userManager->getUserById($this->user->id);
    }
}

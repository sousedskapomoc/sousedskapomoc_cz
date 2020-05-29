<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Forms\Controls\BaseControl;
use SousedskaPomoc\Bootstrap;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

class EditVolunteerForm extends Control
{
    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    private $volunteerRepository;

    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var Passwords */
    private $passwords;

    /** @var \SousedskaPomoc\Repository\RoleRepository */
    private $roleRepository;

    public $onFinish;

    public function __construct(
        VolunteerRepository $volunteerRepository,
        Translator $translator,
        Passwords $passwords,
        RoleRepository $roleRepository
    ) {
        $this->volunteerRepository = $volunteerRepository;
        $this->translator = $translator;
        $this->passwords = $passwords;
        $this->roleRepository = $roleRepository;
    }


    public function createComponentEditVolunteerForm()
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

        /** @var \SousedskaPomoc\Entities\Volunteer $userDetails */
        $userDetails = $this->volunteerRepository->getById($this->getPresenter()->user->getId());

        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;

        $roles = [
            0 => $this->translator->translate('templates.courier.title'),
            1 => $this->translator->translate('templates.operator.title'),
            2 => $this->translator->translate('templates.seamstress.title'),
            3 => $this->translator->translate('templates.coordinator.title'),
        ];

        //@TODO-refactor to add medical coordinator
        if ($this->getPresenter()->user->isInRole('courier')) {
            $roleDefault = 0;
        }
        if ($this->getPresenter()->user->isInRole('operator')) {
            $roleDefault = 1;
        }
        if ($this->getPresenter()->user->isInRole('seamstress')) {
            $roleDefault = 2;
        }
        if ($this->getPresenter()->user->isInRole('coordinator')) {
            $roleDefault = 3;
        }

        if (isset($roleDefault)) {
            $form->addSelect(
                'role',
                $this->translator->translate('forms.registerCoordinator.role'),
                $roles
            )
                ->setDefaultValue($roleDefault);
        }

        $form->addHidden('id');
        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addPassword("password", "Nové heslo");
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));

        if ($this->getPresenter()->user->isInRole('courier')) {
            $form->addSelect('car', $this->translator->translate('forms.registerCoordinator.carLabel'), $cars)
                ->setRequired($this->translator->translate('forms.registerCoordinator.carRequired'));
        }

        $form->setDefaults(
            [
                'personName' => $userDetails->getPersonName(),
                'personEmail' => $userDetails->getPersonEmail(),
                'personPhone' => $userDetails->getPersonPhone(),
                'town' => $userDetails->getAddress()->getCity(),
                'id' => $userDetails->getId(),
            ]
        );

        $form->addSubmit('coordinatorEditFormSubmit', $this->translator->translate('templates.profile.button'));
        $form->onSuccess[] = [$this, "processUpdate"];

        return $form;
    }

    public function processUpdate(BootstrapForm $form)
    {
        $values = $form->getValues();
        $finalRoles = [];

        if (isset($values['role'])) {
            switch ($values['role']) {
                case 0:
                    $finalRole = $this->roleRepository->getByName('courier');
                    break;
                case 1:
                    $finalRole = $this->roleRepository->getByName('operator');
                    break;
                case 2:
                    $finalRole = $this->roleRepository->getByName('seamstress');
                    break;
                case 3:
                    $finalRole = $this->roleRepository->getByName('coordinator');
                    break;
                default:
                    break;
            }
        }
        if ($this->presenter->user->isInRole('superuser')) {
            $finalRole = $this->roleRepository->getByName('superuser');
        }
        if ($this->presenter->user->isInRole('admin')) {
            $finalRole = $this->roleRepository->getByName('admin');
        }
        if ($values->password == null) {
            unset($values->password);
        } else {
            $values->password = $this->passwords->hash($values->password);
        }
        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($values->id);
        if ($user->getId() != $values->id) {
            $form->addError($this->translator->translate('templates.profile.fail'));
        } else {
            if (isset($values->password)) {
                $user->setPassword($this->passwords->hash($values->password));
            }
            $user->setPersonEmail($values->personEmail);
            $user->setPersonPhone($values->personPhone);
            $user->setPersonName($values->personName);
            $user->setRole($finalRole);

            $this->volunteerRepository->update($values->id, $user);

            $this->onFinish($this);
        }
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/edit.latte');
        $this->template->render();
    }
}

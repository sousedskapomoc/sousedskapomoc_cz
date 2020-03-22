<?php


namespace SousedskaPomoc\Components;


use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Forms\Controls\BaseControl;
use SousedskaPomoc\Bootstrap;
use SousedskaPomoc\Repository\VolunteerRepository;
use Nette\Security\Passwords;

class EditVolunteerFormControl extends Control
{
    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    private $volunteerRepository;

    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var Passwords */
    private $passwords;


    public function __construct(
        VolunteerRepository $volunteerRepository,
        Translator $translator,
        Passwords $passwords
    )
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->translator = $translator;
        $this->passwords = $passwords;
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

        $rolesDefault = [];
        if ($this->getPresenter()->user->isInRole('courier')) {
            array_push($rolesDefault, 0);
        }
        if ($this->getPresenter()->user->isInRole('operator')) {
            array_push($rolesDefault, 1);
        }
        if ($this->getPresenter()->user->isInRole('seamstress')) {
            array_push($rolesDefault, 2);
        }
        if ($this->getPresenter()->user->isInRole('coordinator')) {
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

        $form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));

        if ($this->getPresenter()->user->isInRole('courier')) {
            $form->addSelect('car', $this->translator->translate('forms.registerCoordinator.carLabel'), $cars)
                ->setRequired($this->translator->translate('forms.registerCoordinator.carRequired'))
                ->setDefaultValue($userDetails->getTransport());
        }

        $form->setDefaults(
            [
                'personName' => $userDetails->getPersonName(),
                'personEmail' => $userDetails->getPersonEmail(),
                'personPhone' => $userDetails->getPersonPhone(),
//                'town' => $userDetails->getAddress(),
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
        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($values->id);
        if ($user->getId() != $values->id) {
            $form->addError($this->translator->translate('templates.profile.fail'));
        } else {
//            if ($values->password != null) {
//                $user->setPassword($this->passwords->hash($values->password));
//            }
            $user->setPersonEmail($values->personEmail);
            $user->setPersonPhone($values->personPhone);
            $user->setPersonName($values->personName);

            $this->volunteerRepository->update($values->id, $user);


            $this->flashMessage($this->translator->translate('templates.profile.success'));
            $this->getPresenter()->redirect("System:profile");
        }
    }

    public function render()
    {
        $this->template->setFile(__DIR__.'/edit.latte');
        $this->template->render();
    }

}
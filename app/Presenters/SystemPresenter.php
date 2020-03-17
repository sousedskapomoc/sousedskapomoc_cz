<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;

final class SystemPresenter extends BasePresenter
{
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
        ];
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
        $form->addHidden('role', 'courier');

        $form->addHidden('id');
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

        $form->setDefaults(
            [
                'personName' => $userDetails->personName,
                'personEmail' => $userDetails->personEmail,
                'personPhone' => $userDetails->personPhone,
                'town' => $userDetails->town,
                'car' => $userDetails->car,
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

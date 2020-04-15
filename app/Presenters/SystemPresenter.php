<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Forms\Form;
use Nette\Security\Passwords;
use SousedskaPomoc\Components\IEditVolunteerFormInterface;

final class SystemPresenter extends BasePresenter
{
    /** @var \Nette\Security\Passwords */
    protected $passwords;

    /** @var \SousedskaPomoc\Components\IEditVolunteerFormInterface */
    protected $editVolunteerForm;

    public function injectEditVolunteerForm(IEditVolunteerFormInterface $editVolunteerForm)
    {
        $this->editVolunteerForm = $editVolunteerForm;
    }

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

    public function createComponentRegisterAddress()
    {
        $form = new BootstrapForm();
        $form->addText("town", $this->translator->translate('forms.createRegisterAddr.stateChangeSuccess'));
        $form->addHidden("selectedTown")->setRequired( $this->translator->translate('forms.createRegisterAddr.myTownHidden'));
        $form->addSubmit("addressSubmit", $this->translator->translate('forms.createRegisterAddr.saveAddr'));
        $form->onSuccess[] = [$this, "updateAddress"];
        return $form;
    }

    public function updateAddress(BootstrapForm $form)
    {
        $values = $form->getValues();
        $this->userManager->updateTown($values->selectedTown, $this->user->getId());
        $this->flashMessage($this->translator->translate('forms.addressUpdate.changeSuccessful'), 'danger');
        $this->redirect("System:profile");
    }


    public function createComponentEditForm()
    {
        return $this->editVolunteerForm->create();
    }


    public function renderProfile()
    {
        $this->template->userDetails = $this->userManager->getUserById($this->user->id);
    }
}

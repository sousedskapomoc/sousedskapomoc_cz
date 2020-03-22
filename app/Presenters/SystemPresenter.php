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


    /** @var IEditVolunteerFormInterface */
    private $editVolunteerFactory;


    public function injectPasswords(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }


    public function injectEditVolunteerFactory (IEditVolunteerFormInterface $editVolunteerForm) {
        $this->editVolunteerFactory = $editVolunteerForm;
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
        return $this->editVolunteerFactory->create();
    }



    public function renderProfile()
    {
        $this->template->userDetails = $this->userManager->getUserById($this->user->id);
    }
}

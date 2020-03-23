<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Forms\Form;
use Nette\Security\Passwords;
use SousedskaPomoc\Components\IEditVolunteerFormInterface;
use SousedskaPomoc\Entities\Role;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

final class SystemPresenter extends BasePresenter
{
    /** @var \Nette\Security\Passwords */
    protected $passwords;


    /** @var IEditVolunteerFormInterface */
    private $editVolunteerFactory;

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $ordersRepository;

    /** @var \SousedskaPomoc\Repository\RoleRepository */
    protected $roleRepository;


    public function injectPasswords(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }

    public function injectRoleRepository(RoleRepository $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    public function injectOrderRespository(OrderRepository $orderRepository)
    {
        $this->ordersRepository = $orderRepository;
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
            'totalCount' => $this->volunteerRepository->fetchTotalCount(),
            'couriersCount' => $this->roleRepository->countUsers('courier'),
            'operatorsCount' => $this->roleRepository->countUsers('operator'),
            'coordinatorsCount' => $this->roleRepository->countUsers('coordinator'),
            'seamstressCount' => $this->roleRepository->countUsers('seamstress'),
            'usersWithoutAccess' => $this->volunteerRepository->getNonActiveUsers(),
            'uniqueTowns' => $this->addressRepository->countUniqueTowns(),
            'ordersCount' => $this->ordersRepository->fetchCount(),
			'deliveredOrdersCount' => $this->ordersRepository->fetchDeliveredCount(),
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
        $this->template->userDetails = $this->volunteerRepository->getById($this->user->getId());
    }
}

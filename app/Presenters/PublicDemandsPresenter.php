<?php

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\DemandRepository;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

class PublicDemandsPresenter extends BasePresenter
{
    /** @var AddressRepository */
    protected $addressRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    /** @var VolunteerRepository */
    protected $volunteerRepository;

    /** @var DemandRepository */
    protected $demandRepository;

    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectVolunteerRepository(VolunteerRepository $volunteerRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
    }

    public function injectAddressRepository(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function injectPublicDemandsRepository(DemandRepository $demandRepository)
    {
        $this->demandRepository = $demandRepository;
    }

    public function renderDefault()
    {
        $townLimit = $_GET['orderList-where-help'] ?? null;

        if ($townLimit == null) {
            $this->template->demands = $this->demandRepository->getAllUnprocessed();
        } else {
            $this->template->demands = $this->demandRepository->getUnprocessedByTown($townLimit);
            $this->template->selectedTown = $townLimit;
        }
    }

    public function renderDashboard()
    {
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Platnost vašeho přihlášení vypršela. Prosíme přihlašte se znovu.");
            $this->redirect("Sign:in");
        }
        $this->template->volunteer = $this->volunteerRepository->getById($this->user->getId());
        $this->template->demands = $this->demandRepository->getByUser($this->user->getId());
    }

    public function renderDetail($id)
    {
        $this->template->demand = $this->demandRepository->getById($id);
    }

    public function handleSelfAssign($id)
    {
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Abyste mohl(a) pomahát je nutné být přihlašen(á)");
        }

        /** @var Volunteer $volunteer */
        $volunteer = $this->volunteerRepository->find($this->user->getId());
        /** @var Demand $demand */
        $demand = $this->demandRepository->find($id);

        $this->demandRepository->assignDemand($volunteer, $demand);

        $this->flashMessage("Poptávku jsme vám přiřadili můžete se pustit do její realizace");
        $this->redirect("this");
    }
}

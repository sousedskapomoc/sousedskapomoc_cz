<?php

namespace SousedskaPomoc\Presenters;

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

        if ($this->user->isLoggedIn()) {
            $townLimit = $this->template->town;
        }

        if ($townLimit == null) {
            $this->template->demands = $this->demandRepository->getAllUnprocessed();
        } else {
            $this->template->demands = $this->demandRepository->getUnprocessedByTown($townLimit);
            $this->template->selectedTown = $townLimit;
        }
    }

    public function renderDashboard()
    {
        $this->template->volunteer = $this->volunteerRepository->getById($this->user->getId());
        $this->template->demands = $this->demandRepository->getByUser($this->user->getId());
    }

    public function renderDetail($id)
    {
        $this->template->demand = $this->demandRepository->getById($id);
    }

    public function handleSelfAssign($id)
    {
        /** @var Volunteer $volunteer */
        $volunteer = $this->volunteerRepository->find($this->user->getId());
        $this->demandRepository->assignDemand($volunteer, $id);
        $this->flashMessage("Poptávku jsme vám přiřadili můžete si pustit do její realizace");
    }
}

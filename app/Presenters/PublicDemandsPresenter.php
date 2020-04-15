<?php

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\OrderRepository;

class PublicDemandsPresenter extends BasePresenter
{
    /** @var AddressRepository */
    protected $addressRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectAddressRepository(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function renderDefault()
    {
        $townLimit = $_GET['orderList-where-help'] ?? null;

        if ($townLimit == null) {
            $this->template->demands = $this->orderRepository->getAll();
        } else {
            $this->template->demands = $this->orderRepository->getByTown($townLimit);
            $this->template->selectedTown = $townLimit;
        }
    }

    public function renderDetail($id)
    {
        $this->template->demand = $this->orderRepository->getById($id);
    }
}

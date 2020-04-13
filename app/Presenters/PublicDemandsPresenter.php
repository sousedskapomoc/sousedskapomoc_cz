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
        $this->template->demands = $this->orderRepository->getAll();
    }

    public function renderDetail($id) {
        $this->template->demand = $this->orderRepository->getById($id);
    }
}

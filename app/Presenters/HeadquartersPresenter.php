<?php

namespace SousedskaPomoc\Presenters;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Embeddable;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\DemandRepository;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Ublaboo\DataGrid\DataGrid;

class HeadquartersPresenter extends BasePresenter
{
    /** @var OrderRepository */
    protected $orderRepository;

    /** @var DemandRepository */
    protected $demandRepository;

    /** @var VolunteerRepository */
    protected $volunteerRepository;

    /** @var AddressRepository */
    protected $addressRepository;

    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function injectDemandRepository(DemandRepository $demandRepository)
    {
        $this->demandRepository = $demandRepository;
    }

    public function injectVolunteerRepository(VolunteerRepository $volunteerRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
    }

    public function injectAddressRepository(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function beforeRender()
    {
        if (!$this->user->isInRole('admin')) {
            $this->flashMessage("Do této sekce nemáte mít přístup!");
            $this->redirect("System:profile");
        }
        parent::beforeRender(); // TODO: Change the autogenerated stub
    }

    public function createComponentUsersDataGrid()
    {
        $role = $this->presenter->getParameter('role');

        //@TODO - add text filter into address
        $grid = new DataGrid();
        $grid->setDataSource(new ArrayCollection($this->userManager->fetchAllUsersInRoleForGrid($role)));
        $grid->addColumnNumber('id', 'ID uživatele');
        $grid->addColumnText('personName', 'Jméno a příjmení')->setFilterText();
        $grid->addColumnText('personEmail', 'E-mail')->setFilterText();
        $grid->addColumnText('personPhone', 'Telefon')->setFilterText();
        $grid->addColumnText('address', 'Město')->setFilterText();
        $grid->addColumnNumber('active', 'Online')->setFilterText();
        $grid->setDefaultPerPage(100);

        return $grid;
    }

    public function createComponentDemandsDataGrid()
    {
        $grid = new DataGrid();

        //@TODO - add text filter into address
        $grid->setDataSource(new ArrayCollection($this->demandRepository->getAllForGrid()));
        $grid->addColumnNumber('id', 'ID')->setFilterText();
        $grid->addColumnText('deliveryAddress', 'Adresa')->setFilterText();
        $grid->addColumnText('processed', 'Stav poptavky')->setFilterText();
        $grid->addColumnText('contactName', 'Jmeno zadavatele')->setFilterText();
        $grid->addColumnText('contactPhone', 'Telefon zadavatele')->setFilterText();
        $grid->addColumnText('organizationName', 'Jmeno organizace')->setFilterText();
        $grid->addColumnText('deliveryName', 'Jmeno adresata')->setFilterText();
        $grid->addColumnText('deliveryPhone', 'Telefon adresata')->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addAction('approve', 'Schválit', 'approve!')->setClass("btn btn-success btn-sm");
        $grid->addAction('detail', 'Detail', 'Headquarters:demandDetail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', 'X', 'deleteDemand!')->setClass("btn btn-danger btn-sm");

        return $grid;
    }

    public function createComponentOrdersDataGrid()
    {
        $grid = new DataGrid();

        //@TODO - add text filter into address
        $grid->setDataSource(new ArrayCollection($this->orderRepository->getAllForGrid()));
        $grid->addColumnNumber('id', 'ID')
            ->setFilterText();
        $grid->addColumnText('owner', 'Zadavatel')->setFilterText();
        $grid->addColumnText('delivery_address', 'Adresa')->setFilterText();

        $grid->addColumnText('delivery_phone', 'Telefon')->setFilterText();
        $grid->addColumnText('items', 'Položky obj.')->setFilterText();

        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addColumnText('status', 'Status')->setFilterText();
        $grid->addAction('reset', 'Resetovat', 'reset!')->setClass("btn btn-danger btn-sm");
        $grid->addAction('detail', 'Detail', 'Courier:detail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', 'X', 'deleteOrder!')->setClass("btn btn-danger btn-sm");

        return $grid;
    }

    public function renderListUsers($id, $role)
    {
        $this->template->role = $role;
    }

    public function renderTowns()
    {
        $this->template->towns = $this->userManager->getTowns();
    }

    public function handleReset($id)
    {
        $this->orderManager->assignOrder(null, $id, null, 'new');
        $this->flashMessage("Objednávka byla obnovena do výchozího stavu.");
        $this->redirect('Headquarters:orders');
    }

    public function handleDeleteDemand($id)
    {
        /** @var Demand $demand */
        $demand = $this->demandRepository->getById($id);
        if ($demand->getProcessed() == 'declined') {
            $this->flashMessage('Tato objednavka jiz byla zamitnuta.');
            $this->redirect('this');
        } elseif ($demand->getProcessed() == 'approved') {
            $this->flashMessage('Tato objednavka jiz byla schvalena.');
            $this->redirect('this');
        }
        $this->demandRepository->setProcessed($id, 'declined');
        $this->flashMessage("Poptávka byla zamítnuta.");
        $this->redirect('Headquarters:demands');
    }

    public function handleDeleteOrder($id)
    {
        $this->orderManager->remove($id);
        $this->flashMessage("Objednávka byla smazána.");
        $this->redirect('Headquarters:orders');
    }

    public function handleApprove($id)
    {
        /** @var Demand $demand */
        $demand = $this->demandRepository->getById($id);
        if ($demand->getCreatedOrder() != null) {
            $this->flashMessage('Z tohoto pozadavku jiz byla vytvorena objednavka.');
            $this->redirect('this');
        }

        /** @var Order $order */
        $order = new Order();

        /** @var Address $deliveryAddress */
        $deliveryAddress = $demand->getDeliveryAddress();
        $deliveryAddress->addDeliveryOrder($order);

        $order->setDeliveryPhone($demand->getDeliveryPhone());
        $order->setStatus('new');

        //Connect all items into one order
        $finalItems = "";

        if ($demand->getFood() != null) {
            $finalItems = $finalItems . "Jidlo: " .$demand->getFood() . "\n";
        }

        if ($demand->getMedicine() != null ) {
            $finalItems = $finalItems . "Leky: " . $demand->getMedicine() . "\n";
        }

        if ($demand->getVeils() != null ) {
            $finalItems = $finalItems . "Rousky: " . $demand->getVeils() . "\n";
        }

        if ($demand->getOther() != null ) {
            $finalItems = $finalItems . "Ostatni: " . $demand->getOther() . "\n";
        }

        $finalNote = "";
        if ($demand->getIsContactPerson() || $demand->getIsOrganization()) {
            $finalNote = "Kontaktni osoba, ktera vytvorila objednavku\n";
            $finalNote = $finalNote . "Jmeno: " . $demand->getContactName() . "\n";
            $finalNote = $finalNote . "Telefon: " . $demand->getContactPhone() . "\n";
        }

        $order->setItems($finalItems);
        $order->setCustomerNote($finalNote);
        $order->setFromDemand($demand);

        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($this->user->getId());
        $user->addCreatedOrder($order);

        $this->addressRepository->create($deliveryAddress);
        $this->volunteerRepository->update($user->getId(), $user);
        $this->demandRepository->setProcessed($demand->getId(), 'approved');

        $this->redirect('Coordinator:detail', $order->getId());
    }

    public function renderDemandDetail($id)
    {
        $this->template->demand = $this->demandRepository->getById($id);
    }
}

<?php

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Embeddable;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\CallRoulette;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\CallRouletteRepository;
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

    /** @var CallRouletteRepository */
    protected $callRouletteRepository;

    public function injectCallRouletteRepository(CallRouletteRepository $callRouletteRepository)
    {
        $this->callRouletteRepository = $callRouletteRepository;
    }

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
        } else {
            $this->template->topics = $this->callRouletteRepository->getTopics();
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
        $grid->addColumnNumber('active', 'Online')->setFilterText();
        $grid->addColumnText('personName', 'Jméno a příjmení')->setFilterText();
        $grid->addColumnText('personEmail', 'E-mail')->setFilterText();
        $grid->addColumnText('personPhone', 'Telefon')->setFilterText();
        $grid->addColumnText('address', 'Město')->setFilterText();
        $grid->addColumnText('note', 'Poznámka')->setFilterText();
        $grid->addAction('approve', 'Upravit poznámku', 'addNote')
            ->setClass("btn btn-success btn-sm");
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
        $grid
            ->addAction('detail', 'Detail', 'Headquarters:demandDetail')
            ->setClass("btn btn-primary btn-sm");
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

    public function createComponentOrdersReservedDataGrid()
    {
        $grid = new DataGrid();

        //@TODO - add text filter into address
        $grid->setDataSource(new ArrayCollection($this->orderRepository->getAllOldReservedForGrid()));
        $grid->addColumnNumber('id', 'ID')
            ->setFilterText();
        $grid->addColumnText('owner', 'Zadavatel')->setFilterText();
        $grid->addColumnText('delivery_address', 'Adresa')->setFilterText();

        $grid->addColumnText('delivery_phone', 'Telefon')->setFilterText();
        $grid->addColumnText('items', 'Položky obj.')->setFilterText();

        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addColumnDateTime('reservedAt', 'Datum rezervace');
        $grid->addColumnText('coordinatorName', 'Operátor');
        $grid->addColumnText('coordinatorPhone', 'Operátor');
        $grid->addColumnText('status', 'Status')->setFilterText();
        $grid->addAction('reset', 'Resetovat', 'resetReservation!')->setClass("btn btn-danger btn-sm");
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


    public function createComponentAddNote()
    {
        $form = new BootstrapForm;

        $id = $this->getParameter('id');

        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->userManager->getUserById($id);

        $form->addHidden('id');
        $form->addHidden('role');
        $form->addText('note');

        if ($user instanceof Volunteer) {
            $form->setDefaults([
                'id' => $id,
                'note' => $user->getNote(),
                'role' => $user->getRole()->getName(),
            ]);
        }

        $form->addSubmit('submit', 'Uložit poznámku');
        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Form $form, $values)
    {
        $this->volunteerRepository->updateNote($values->id, $values->note);
        $this->flashMessage("Poznámka byla úspěšně uložena.");
        $this->redirect("Headquarters:listUsers", ['role' => $values->role]);
    }


    public function handleReset($id)
    {
        $this->orderManager->assignOrder(null, $id, null, 'new');
        $this->flashMessage("Objednávka byla obnovena do výchozího stavu.");
        $this->redirect('Headquarters:orders');
    }


    public function handleResetReservation($id)
    {
        $this->orderManager->unassignOrderCoordinator($id);
        $this->flashMessage("Objednávka byla obnovena do výchozího stavu.");
        $this->redirect('Headquarters:ordersReserved');
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
            $finalItems = $finalItems . "Jidlo: " . $demand->getFood() . "\n";
        }

        if ($demand->getMedicine() != null) {
            $finalItems = $finalItems . "Leky: " . $demand->getMedicine() . "\n";
        }

        if ($demand->getVeils() != null) {
            $finalItems = $finalItems . "Rousky: " . $demand->getVeils() . "\n";
        }

        if ($demand->getOther() != null) {
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

    public function renderCallRoulette()
    {
        $this->template->calls = $this->callRouletteRepository->findAll();
    }

    public function createComponentNewNote()
    {
        $form = new BootstrapForm();
        $form->addText('description', 'Text poznámky');
        $form->addHidden('callId', $this->presenter->getParameter('id'));
        $form->addHidden('userId', $this->user->getId());
        $form->addSubmit('submit', 'Uložit poznámku');
        $form->onSuccess[] = [$this, "processForm"];
        return $form;
    }

    public function processForm(BootstrapForm $form)
    {
        $values = $form->getValues();
        $user = $this->user->getIdentity()->data;

        $note = [
            'dateTime' => new DateTime("now"),
            'noteTaker' =>
                htmlentities($user['personName']) ?? $user['personEmail'],
            'description' => $values->description
        ];
        /** @var CallRoulette $call */
        $call = $this->callRouletteRepository->find($values->callId);
        $notes = json_decode($call->getNotes() ?? null);

        if ($notes == null) {
            $notes = [$note];
        } else {
            $notes[] = $note;
        }

        $notesJson = Json::encode($notes);
        $call->setNotes($notesJson);

        $this->callRouletteRepository->store($call);

        $this->flashMessage("Poznámka byla uložena");
        $this->redirect('this');
    }

    public function renderPhoneHistory($id)
    {
        $this->template->caller = $this->callRouletteRepository->find($id);
    }

    public function renderPhoneNote($id)
    {
        $this->template->call = $this->callRouletteRepository->find($id);
    }

    public function createComponentNewConferenceCall()
    {
        $form = new BootstrapForm();
        $form->addText('title', 'Název konference');
        $form->addText('callerPhone', 'Tel. číslo účastníka 1')
            ->setRequired("Telefonní čísla 1. účastníka je povinné");
        $form->addText('receiverPhone', 'Tel. číslo účastníka 2')
            ->setRequired("Telefonní čísla 2. účastníka je povinné");
        $form->addSubmit('newConfCallSubmit', 'Založit konference a spojit účastníky');
        $form->onSuccess[] = [$this, 'startConferenceCall'];
        return $form;
    }

    public function startConferenceCall(BootstrapForm $form)
    {
        $values = $form->getValues();
        $this->redirect('this');
    }
}

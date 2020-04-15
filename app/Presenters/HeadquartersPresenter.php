<?php

namespace SousedskaPomoc\Presenters;

use Doctrine\ORM\Mapping\Embeddable;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\DemandRepository;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Ublaboo\DataGrid\DataGrid;

class HeadquartersPresenter extends BasePresenter
{
    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    /** @var \SousedskaPomoc\Repository\DemandRepository */
    protected $demandRepository;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;

    /** @var \SousedskaPomoc\Repository\AddressRepository */
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
            $this->flashMessage($this->translator->translate('messages.permission.permissionDenied'));
            $this->redirect("System:profile");
        }
        parent::beforeRender(); // TODO: Change the autogenerated stub
    }

    public function createComponentUsersDataGrid()
    {
        $role = $this->presenter->getParameter('role');

        //@TODO - add text filter into address
        $grid = new DataGrid();
        $grid->setDataSource($this->userManager->fetchAllUsersInRole($role));
        $grid->addColumnNumber('id', $this->translator->translate('templates.gridCreateUser.userID') );
        $grid->addColumnText('personName', $this->translator->translate('templates.gridCreateUser.fullName') )->setFilterText();
        $grid->addColumnText('personEmail', $this->translator->translate('templates.gridCreateUser.eMail') )->setFilterText();
        $grid->addColumnText('personPhone', $this->translator->translate('templates.gridCreateUser.phone'))->setFilterText();
        $grid->addColumnText('address', $this->translator->translate('templates.gridCreateUser.address') )
            ->setRenderer(function ($item) {
                if ($item->getAddress() != null) {
                    return $item->getAddress()->getCity();
                } else {
                    return $this->translator->translate('templates.gridCreateUser.notSpecified');
                }
            });
        $grid->addColumnNumber('active', $this->translator->translate('templates.gridCreateUser.Online'))->setFilterText();
        $grid->setDefaultPerPage(100);

        return $grid;
    }

    public function createComponentDemandsDataGrid()
    {
        $grid = new DataGrid();

        //@TODO - add text filter into address
<<<<<<< HEAD
        $grid->setDataSource($this->demandRepository->getAll());
        $grid->addColumnNumber('id', 'ID')->setFilterText();
        $grid->addColumnText('deliveryAddress', 'Adresa')
=======
        $grid->setDataSource($this->orderManager->fetchAllWebDemands());
        $grid->addColumnNumber('id', $this->translator->translate('templates.gridCreateDemand.id') )->setFilterText();
        $grid->addColumnText('id_volunteers', $this->translator->translate('templates.gridCreateDemand.submitter') );
        $grid->addColumnText('delivery_address', $this->translator->translate('templates.gridCreateDemand.address') )
>>>>>>> presenters translations
            ->setRenderer(function ($item) {
                if ($item->getDeliveryAddress() != null) {
                    return $item->getDeliveryAddress()->getCity();
                } else {
                    return $this->translator->translate('templates.gridCreateDemand.notSpecified') ;
                }
            });
<<<<<<< HEAD
        $grid->addColumnText('phone', 'Telefon')->setFilterText();
        $grid->addColumnText('processed', 'Stav poptavky')->setFilterText();
        $grid->addColumnText('name', 'Položky obj.')->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addAction('approve', 'Schválit', 'approve!')->setClass("btn btn-success btn-sm");
        $grid->addAction('detail', 'Detail', 'Headquarters:demandDetail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', 'X', 'deleteDemand!')->setClass("btn btn-danger btn-sm");
=======
        $grid->addColumnText('delivery_phone', $this->translator->translate('templates.gridCreateDemand.phone') )->setFilterText();
        $grid->addColumnText('order_items', $this->translator->translate('templates.gridCreateDemand.orderedItems') )->setFilterText();
        $grid->addFilterSelect('status', $this->translator->translate('templates.gridCreateDemand.orderStatus') , []);
        $grid->addColumnDateTime('createdAt',  $this->translator->translate('templates.gridCreateDemand.addedTime') );
        $grid->addAction('approve', $this->translator->translate('templates.gridCreateDemand.actionConfirm'),
                                     'approve!')->setClass("btn btn-success btn-sm");
        $grid->addAction('detail', $this->translator->translate('templates.gridCreateDemand.address'),
                                     'Courier:detail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', $this->translator->translate('templates.gridCreateDemand.actionDelete'),
                                     'deleteDemand!')->setClass("btn btn-danger btn-sm");
>>>>>>> presenters translations

        return $grid;
    }

    public function createComponentOrdersDataGrid()
    {
        $grid = new DataGrid();

        //@TODO - add text filter into address
        $grid->setDataSource($this->orderRepository->getAll());
        $grid->addColumnNumber('id',  $this->translator->translate('templates.gridCreateOdrers.id' ) )->setFilterText();
        $grid->addColumnText('owner', $this->translator->translate('templates.gridCreateOdrers.owner' ) )
            ->setRenderer(function ($item) {
                if ($item->getOwner()->getPersonName() != null) {
                    return $item->getOwner()->getPersonName();
                } else {
                    return $this->translator->translate('templates.gridCreateOdrers.notSpecified' );
                }
            });
        $grid->addColumnText('delivery_address', $this->translator->translate('templates.gridCreateOdrers.address' ))
            ->setRenderer(function ($item) {
                if ($item->getDeliveryAddress() != null) {
                    return $item->getDeliveryAddress()->getCity();
                } else {
                    return $this->translator->translate('templates.gridCreateOdrers.notSpecified' );
                }
            });
        $grid->addColumnText('delivery_phone', $this->translator->translate('templates.gridCreateOdrers.phone' ) )->setFilterText();
        $grid->addColumnText('items', $this->translator->translate('templates.gridCreateOdrers.itemsList' ))->setFilterText();

        $grid->addColumnDateTime('createdAt', $this->translator->translate('templates.gridCreateOdrers.addedTime' ));
        $grid->addColumnText('status', $this->translator->translate('templates.gridCreateOdrers.status' ) )->setFilterText();
        $grid->addAction('reset', $this->translator->translate('templates.gridCreateOdrers.reset' ),
                                     'reset!')->setClass("btn btn-danger btn-sm");
        $grid->addAction('detail', $this->translator->translate('templates.gridCreateOdrers.detail' ),
                                     'Courier:detail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', $this->translator->translate('templates.gridCreateOdrers.delete' ),
                                     'deleteOrder!')->setClass("btn btn-danger btn-sm");

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
        $this->flashMessage($this->translator->translate('messages.handleReset.reseted' ));
        $this->redirect('Headquarters:orders');
    }

    public function handleDeleteDemand($id)
    {
<<<<<<< HEAD
        /** @var \SousedskaPomoc\Entities\Demand $demand */
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
=======
        $this->orderManager->remove($id);
        $this->flashMessage($this->translator->translate('messages.handleDeleteDemand.deleted' ));
>>>>>>> presenters translations
        $this->redirect('Headquarters:demands');
    }

    public function handleDeleteOrder($id)
    {
        $this->orderManager->remove($id);
        $this->flashMessage($this->translator->translate('messages.handleDeleteOrder.deleted' ));
        $this->redirect('Headquarters:orders');
    }

    public function handleApprove($id)
    {
<<<<<<< HEAD
        /** @var \SousedskaPomoc\Entities\Demand $demand */
        $demand = $this->demandRepository->getById($id);
        if ($demand->getCreatedOrder() != null) {
            $this->flashMessage('Z tohoto pozadavku jiz byla vytvorena objednavka.');
            $this->redirect('this');
        }

        /** @var Order $order */
        $order = new Order();

        /** @var \SousedskaPomoc\Entities\Address $deliveryAddress */
        $deliveryAddress = $demand->getDeliveryAddress();
        $deliveryAddress->addDeliveryOrder($order);

        $order->setDeliveryPhone($demand->getPhone());
        $order->setStatus('new');
        $order->setItems($demand->getItems());
        $order->setCustomerNote($demand->getName());
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
=======
        $this->orderManager->changeStatus($id, 'new');
        $this->flashMessage($this->translator->translate('messages.handleApprove.approved' ));
        $this->redirect('Headquarters:demands');
>>>>>>> presenters translations
    }
}

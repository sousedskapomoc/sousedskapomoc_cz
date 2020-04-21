<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

final class SeamstressPresenter extends BasePresenter
{
    /** @var OrderManager */
    protected $orderManager;

    /** @var \SousedskaPomoc\Repository\AddressRepository */
    protected $addressRepository;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;

    public function beforeRender()
    {
        parent::beforeRender(); // TODO: Change the autogenerated stub

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public function injectAddressRepository(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function injectVolunteerRepository(VolunteerRepository $volunteerRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
    }


    public function createComponentPostOrder()
    {
        $form = new BootstrapForm;

        $form->addText('pickupAddress', $this->translator->translate('forms.postOrder.addressPick'))
            ->setRequired($this->translator->translate('forms.postOrder.addressRequired'))
            ->setPlaceholder($this->translator->translate('forms.postOrder.addressPlaceholder'));
        $form->addText('items', $this->translator->translate('templates.seamstress.itemsLabel'))
            ->setPlaceholder( $this->translator->translate('forms.postOrder.tenPiecesRequirement') )
            ->setRequired( $this->translator->translate('forms.postOrder.enterNumForPickUp') );
        $form->addHidden('pickupId');

        $form->addSubmit('addOrderFormSubmit', $this->translator->translate('forms.postOrder.sendDemand') );
        $form->onSuccess[] = [$this, "processAdd"];

        return $form;
    }


    public function processAdd(BootstrapForm $form)
    {
        $values = $form->getValues();

        /** @var Order $order */
        $order = new Order();
        $order->setItems($values->items);
        $order->setStatus('new');
        $order->setCourier(null);
        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($this->presenter->user->getId());
        $user->addCreatedOrder($order);
        $order->setOwner($user);

        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $baseUri = "https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid=";
        $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
        $pickupResponse = $client->get($baseUri . $values->pickupId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
        $pickupContent = $pickupResponse->getBody()->getContents();
        $pickupContent = json_decode($pickupContent);
        //Address information
        $addrPickup = $pickupContent->response->view['0']->result['0']->location->address;
        //HERE maps Id
        $pickupId = $pickupContent->response->view['0']->result['0']->location->locationId;
        //array with latitude and longtitude
        $pickupGps = $pickupContent->response->view['0']->result['0']->location->displayPosition;

        /** @var Address $pickupAddress */
        $pickupAddress = new Address();
        $pickupAddress->setCity($addrPickup->city);
        $pickupAddress->setState($addrPickup->state);
        $pickupAddress->setLocationId($pickupId);
        $pickupAddress->setCountry($addrPickup->country);
        $pickupAddress->setDistrict($addrPickup->county);
        $pickupAddress->setPostalCode($addrPickup->postalCode);
        $pickupAddress->setLongitude($pickupGps->longitude);
        $pickupAddress->setLatitude($pickupGps->latitude);
        $pickupAddress->addPickupOrder($order);


        try {
            $this->addressRepository->create($pickupAddress);
            $this->flashMessage($this->translator->translate('messages.order.orderSuccess'));
            $this->redirect("Seamstress:dashboard");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }


    public function renderDashboard()
    {
        $this->template->orders = $this->orderManager->findAllForUser($this->user->getId());
        $this->template->userOnline = $this->userManager->isOnline($this->user->getId());
    }

    public function handleToggleActive($active)
    {
        $this->userManager->setOnline($this->user->getId(), $active);
        $this->flashMessage( $this->translator->translate('messages.toggleActive.stateChangeSuccess'));
        $this->redirect('this');
    }
}

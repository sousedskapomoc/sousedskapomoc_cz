<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\DemandRepository;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

class CreateOrderFormControl extends Control
{
    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var AddressRepository */
    protected $addressRepository;

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;



    public function __construct(
        Translator $translator,
        AddressRepository $addrRepository,
        OrderRepository $orderRepository,
        VolunteerRepository $volunteerRepository
    ) {
        $this->translator = $translator;
        $this->addressRepository = $addrRepository;
        $this->orderRepository = $orderRepository;
        $this->volunteerRepository = $volunteerRepository;
    }


    public function createComponentCreateOrderForm()
    {
        $form = new BootstrapForm;

        $form->addText('pickupAddress', 'Adresa vyzvednuti')
            ->setPlaceholder('Ulice včetně čísla popisného');
        $form->addText('deliveryAddress', 'Adresa doruceni')
            ->setRequired('Prosim zadejte adresu kam je treba objednavka dorucit.');
        $form->addText('phone', 'Zadejte telefon adresata')
            ->setRequired('Prosim zadejte telefon komu dorucujeme');
        $form->addTextArea('items', "Polozky objednavky")
            ->setRequired('Prosim sdelte nam co by jste potrebovali.');
        $form->addText('customerNote', 'Poznamka adresata');
        $form->addHidden('pickupId');
        $form->addHidden('deliveryId');

        $form->addSubmit('addOrderFormSubmit', 'Odeslat poptavku');
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
        $order->setCustomerNote($values->customerNote);
        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($this->presenter->user->getId());
        $user->addCreatedOrder($order);
        $order->setOwner($user);
        $order->setDeliveryPhone($values->phone);

        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $baseUri = "https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid=";
        $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
        $pickupResponse = $client->get($baseUri . $values->pickupId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
        $deliveryResponse = $client->get($baseUri . $values->deliveryId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
        $pickupContent = $pickupResponse->getBody()->getContents();
        $deliveryContent = $deliveryResponse->getBody()->getContents();

        $pickupContent = json_decode($pickupContent);
        $deliveryContent = json_decode($deliveryContent);

        //Address information
        $addrPickup = $pickupContent->response->view['0']->result['0']->location->address;
        $addrDelivery = $deliveryContent->response->view['0']->result['0']->location->address;

        //HERE maps Id
        $pickupId = $pickupContent->response->view['0']->result['0']->location->locationId;
        $deliveryId = $deliveryContent->response->view['0']->result['0']->location->locationId;

        //array with latitude and longtitude
        $pickupGps = $pickupContent->response->view['0']->result['0']->location->displayPosition;
        $deliveryGps = $deliveryContent->response->view['0']->result['0']->location->displayPosition;

        /** @var Address $pickupAddress */
        $pickupAddress = new Address();
        $pickupAddress->setCity($addrPickup->city);
        $pickupAddress->setState($addrPickup->state);
        $pickupAddress->setLocationId($pickupId);
        $pickupAddress->setCountry($addrPickup->country);
        $pickupAddress->setDistrict($addrPickup->county);
        $pickupAddress->setPostalCode($addrPickup->postalCode);
        $pickupAddress->setStreet($addrPickup->street);
        $pickupAddress->setHouseNumber($addrPickup->houseNumber);
        $pickupAddress->setLongitude($pickupGps->longitude);
        $pickupAddress->setLatitude($pickupGps->latitude);
        $pickupAddress->addPickupOrder($order);

        /** @var Address $deliveryAddress */
        $deliveryAddress = new Address();
        $deliveryAddress->setCity($addrDelivery->city);
        $deliveryAddress->setState($addrDelivery->state);
        $deliveryAddress->setLocationId($deliveryId);
        $deliveryAddress->setCountry($addrDelivery->country);
        $deliveryAddress->setDistrict($addrDelivery->county);
        $deliveryAddress->setPostalCode($addrDelivery->postalCode);
        $deliveryAddress->setLongitude($deliveryGps->longitude);
        $deliveryAddress->setLatitude($deliveryGps->latitude);
        $deliveryAddress->setStreet($addrDelivery->street);
        $deliveryAddress->setHouseNumber($addrDelivery->houseNumber);
        $deliveryAddress->addDeliveryOrder($order);


        try {
            $this->addressRepository->create($pickupAddress);
            $this->addressRepository->create($deliveryAddress);
            $this->presenter->flashMessage('Objednavka byla vytvorena.');
            $this->presenter->redirect("Homepage:default");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/add.latte');
        $this->template->render();
    }
}

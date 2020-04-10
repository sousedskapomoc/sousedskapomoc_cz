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

class EditOrderFormControl extends Control
{
    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var AddressRepository */
    protected $addressRepository;

    /** @var \SousedskaPomoc\Repository\OrderRepository */
    protected $orderRepository;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;

    protected $orderId;

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


    public function createComponentEditOrderForm()
    {
        $form = new BootstrapForm;

        $form->addText('pickupAddress', 'Adresa vyzvednuti')
            ->setPlaceholder('Ulice včetně čísla popisného');
        $form->addText('deliveryAddress', 'Adresa doruceni')
            ->setRequired('Prosim zadejte adresu kam je treba objednavka dorucit.');
        $form->addHidden('pickupId');
        $form->addHidden('deliveryId');

        $this->orderId = $this->presenter->getParameter('id');
        /** @var Order $order */
        $order = $this->orderRepository->getById($this->orderId);

        $defaults = [];
        if ($order->getPickupAddress()) {
            $defaults['pickupAddress'] = $order->getPickupAddress()->getCity();
        }
        if ($order->getDeliveryAddress()) {
            $defaults['deliveryAddress'] = $order->getDeliveryAddress()->getCity();
        }
        $form->setDefaults($defaults);

        $form->addSubmit('editOrderFormSubmit', 'Ulozit objednakvu');
        $form->onSuccess[] = [$this, "processEdit"];

        return $form;
    }

    public function processEdit(BootstrapForm $form)
    {
        $values = $form->getValues();

        /** @var Order $order */
        $order = $this->orderRepository->getById($this->orderId);
        /** @var Address $dAdd */
        $dAdd = $order->getDeliveryAddress();
        $dAdd->removeDeliveryOrder($order);

        /** @var Address $pAdd */
        $pAdd = $order->getPickupAddress();
        if ($pAdd != null) {
            $pAdd->removePickupOrder($order);
        }

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
        $deliveryAddress->addDeliveryOrder($order);


        try {
            $this->addressRepository->create($pickupAddress);
            $this->addressRepository->create($deliveryAddress);
            $this->presenter->flashMessage('Objednavka byla upravena.');
            $this->presenter->redirect("Coordinator:detail", $this->orderId);
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

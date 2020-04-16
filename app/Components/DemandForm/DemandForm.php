<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Components\Suggester\ISuggesterTownInterface;
use SousedskaPomoc\Components\Suggester\Town;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\DemandRepository;

class DemandFormControl extends Control
{
    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var AddressRepository */
    protected $addressRepository;

    /** @var DemandRepository */
    protected $demandRepository;

    /** @var ISuggesterTownInterface */
    protected $townSuggester;

    public function __construct(
        Translator $translator,
        AddressRepository $addrRepository,
        DemandRepository $demandRepository,
        ISuggesterTownInterface $townSuggester
    )
    {
        $this->translator = $translator;
        $this->addressRepository = $addrRepository;
        $this->demandRepository = $demandRepository;
        $this->townSuggester = $townSuggester;
    }

    public function createComponentTownSuggester()
    {
        return $this->townSuggester->create();
    }

    public function createComponentDemandForm()
    {
        $form = new BootstrapForm;

        $form->addText('town', 'Zadejte ulici vcetne cisla popisneho')
            ->setPlaceholder('Na Vypichu 25')
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));
        $form->addText('name', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('phone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addTextArea('items', "Polozky objednavky")
            ->setRequired('Prosim sdelte nam co by jste potrebovali.');
        $form->addHidden('locationId');

        $form->addSubmit('addDemandFormSubmit', 'Odeslat poptavku');
        $form->onSuccess[] = [$this, "processAdd"];

        return $form;
    }

    public function processAdd(BootstrapForm $form)
    {
        $values = $form->getValues();

        $demand = new Demand();
        $demand->setProcessed('new');
        $demand->setName($values->name);
        $demand->setPhone($values->phone);
        $demand->setItems($values->items);


        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $baseUri = "https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid=";
        $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
        $response = $client->get($baseUri . $values->locationId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
        $content = $response->getBody()->getContents();

        $content = json_decode($content);

        //Address information
        $addr = $content->response->view['0']->result['0']->location->address;

        //HERE maps Id
        $locationId = $content->response->view['0']->result['0']->location->locationId;

        //array with latitude and longtitude
        $gps = $content->response->view['0']->result['0']->location->displayPosition;

        /** @var Address $address */
        $address = new Address();
        $address->setCity($addr->city);
        $address->setState($addr->state);
        $address->setLocationId($locationId);
        $address->setCountry($addr->country);
        $address->setDistrict($addr->county);
        $address->setPostalCode($addr->postalCode);
        $address->setLongitude($gps->longitude);
        $address->setLatitude($gps->latitude);
        $address->addDemandOrder($demand);


        try {
            $this->addressRepository->create($address);
            $this->presenter->flashMessage('Poptavka byla uspesne odeslana ke zpracovani.');
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

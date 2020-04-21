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
    ) {
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
            ->setPlaceholder('Na Vypichu 25');
        $form->addText('contactName', $this->translator->translate('forms.registerCoordinator.nameLabel'));
        $form->addText('organizationName', $this->translator->translate('forms.registerCoordinator.nameLabel'));
        $form->addText('contactPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'));
        $form->addText('deliveryName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('deliveryPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addText('deliveryAddress', "Zadejte adresu doruceni")
            ->setPlaceholder("Zadejte prosím ulici a č.p.");
        $form->addHidden('deliveryId')
            ->setRequired('Prosím zvolte adresu doručení z našeptávače.');
        $form->addTextArea('food', "Polozky objednavky");
        $form->addTextArea('medicine', "Polozky objednavky");
        $form->addTextArea('veils', "Polozky objednavky");
        $form->addTextArea('other', "Polozky objednavky");
        $form->addHidden('locationId');
        $form->addHidden('isOrganisation')->setDefaultValue('0');
        $form->addHidden('isContactPerson')->setDefaultValue('0');

        $form->addSubmit('addDemandFormSubmit', 'Pokračovat');
        $form->onSuccess[] = [$this, "processAdd"];

        return $form;
    }

    public function processAdd(BootstrapForm $form)
    {
        $values = $form->getValues();

        $demand = new Demand();
        $demand->setProcessed('new');
        $demand->setDeliveryName($values->deliveryName);
        $demand->setDeliveryPhone($values->deliveryPhone);
        $demand->setContactName($values->contactName);
        $demand->setContactPhone($values->contactPhone);
        $demand->setOrganizationName($values->organizationName);
        $demand->setIsOrganization($values->isOrganisation);
        $demand->setIsContactPerson($values->isContactPerson);

        $demand->setFood($values->food);
        $demand->setVeils($values->veils);
        $demand->setMedicine($values->medicine);
        $demand->setOther($values->other);


        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $baseUri = "https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid=";
        $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
        $response = $client->get($baseUri . $values->deliveryId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
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

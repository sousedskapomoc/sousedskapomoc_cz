<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Forms\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use SousedskaPomoc\Components\IEditVolunteerFormInterface;
use SousedskaPomoc\Components\Suggester\ISuggesterTownInterface;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Demand;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

final class SystemPresenter extends BasePresenter
{
    /** @var \Nette\Security\Passwords */
    protected $passwords;

    /** @var \SousedskaPomoc\Components\IEditVolunteerFormInterface */
    protected $editVolunteerForm;

    /** @var ISuggesterTownInterface */
    protected $townSuggester;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;

    /** @var \SousedskaPomoc\Repository\AddressRepository */
    protected $addressRepository;

    public function injectEditVolunteerForm(IEditVolunteerFormInterface $editVolunteerForm)
    {
        $this->editVolunteerForm = $editVolunteerForm;
    }

    public function injectPasswords(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }

    public function injectTownSuggester(ISuggesterTownInterface $suggesterTown)
    {
        $this->townSuggester = $suggesterTown;
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
        parent::beforeRender();

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }


    public function renderDashboard()
    {
        $this->template->statistics = [
            'totalCount' => $this->userManager->fetchTotalCount(),
            'couriersCount' => $this->userManager->fetchCountByRole('courier'),
            'operatorsCount' => $this->userManager->fetchCountByRole('operator'),
            'coordinatorsCount' => $this->userManager->fetchCountByRole('coordinator'),
            'seamstressCount' => $this->userManager->fetchCountByRole('seamstress'),
            'usersWithoutAccess' => $this->userManager->fetchCountBy(['password' => null]),
            'uniqueTowns' => $this->userManager->fetchUniqueTownsCount(),
            'ordersCount' => $this->orderManager->fetchCount(),
            'deliveredOrdersCount' => $this->orderManager->fetchDeliveredCount(),
        ];
    }

    public function createComponentTownSuggester()
    {
        return $this->townSuggester->create();
    }

    public function renderEnterTown()
    {
        $locationId = $this->getParameter('addressHereMapsId');
        if (isset($locationId)) {
            $this->updateAddress($locationId);
        }
    }

    public function updateAddress($locationId)
    {
        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getById($this->user->getId());

        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $baseUri = "https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid=";
        $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
        $response = $client->get($baseUri . $locationId . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
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
        $address->addVolunteer($user);


        try {
            $this->addressRepository->create($address);
            $this->flashMessage('Adresa byla uspesne upravena.');
            $this->redirect("System:profile");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }


    public function createComponentEditForm()
    {
        return $this->editVolunteerForm->create();
    }


    public function renderProfile()
    {
        $this->template->userDetails = $this->userManager->getUserById($this->user->id);
    }
}

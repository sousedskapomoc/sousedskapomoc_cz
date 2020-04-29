<?php

namespace SousedskaPomoc\Components;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Components\Suggester\ISuggesterTownInterface;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Nette\Security\Passwords;
use SousedskaPomoc\Entities\Transport;
use SousedskaPomoc\Repository\TransportRepository;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Entities\Role;

class RegisterVolunteerFormControl extends Control
{
    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    private $volunteerRepository;

    /** @var \Kdyby\Translation\Translator */
    private $translator;

    /** @var Passwords */
    private $passwords;

    /** @var  TransportRepository */
    private $transportRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var \SousedskaPomoc\Components\Mail */
    private $mail;

    /** @var AddressRepository */
    private $addressRepository;

    /** @var ISuggesterTownInterface */
    private $townSuggester;

    private $role;


    public function __construct(
        VolunteerRepository $volunteerRepository,
        Translator $translator,
        Passwords $passwords,
        TransportRepository $transport,
        $role,
        RoleRepository $roleRepository,
        Mail $mail,
        AddressRepository $addrRepository,
        ISuggesterTownInterface $townSuggester
    )
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->translator = $translator;
        $this->passwords = $passwords;
        $this->transportRepository = $transport;
        if ($role != null) {
            $this->role = $role;
        }
        $this->roleRepository = $roleRepository;
        $this->mail = $mail;
        $this->addressRepository = $addrRepository;
        $this->townSuggester = $townSuggester;
    }

    public function createComponentTownSuggester()
    {
        return $this->townSuggester->create();
    }


    public function createComponentRegisterVolunteerForm()
    {
        $form = new BootstrapForm;
//        $form->renderMode = RenderMode::VERTICAL_MODE;

        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));
        $form->addRadioList(
            'role',
            'S čím můžeme pomoci:',
            [
                0 => 'Doručit nákup nebo léky',
                1 => 'Stát se dobrým sousedem',
                2 => 'Ušít roušky',
                3 => 'Pomáhat ostatním s doručením',
                4 => 'Dodat materiál',
                5 => 'Pomoci 3D tiskem'
            ]
        );
        $form->addRadioList(
            'transport',
            'Vyberte svůj dopravní prostředek:',
            [
                1 => 'Malé auto',
                2 => 'Velké auto',
                3 => 'Malá dodávka',
                4 => 'Velká dodávka',
                5 => 'Kolo',
                6 => 'Motorka ',
                7 => 'Chůze'
            ]
        )->setDefaultValue(7);

        if ($this->presenter->getParameter('addressHereMapsId')) {
            $form->addHidden('locationId')
                ->setDefaultValue($this->presenter->getParameter('addressHereMapsId'));
        } else {
            $form->addHidden('locationId');
        }

        $form->addSubmit('addVolunteerFormSubmit', $this->translator->translate('templates.profile.send'));
        $form->onSuccess[] = [$this, "processAdd"];

        return $form;
    }

    public function processAdd(BootstrapForm $form)
    {
        $values = $form->getValues();

        $user = new Volunteer();
        $user->setActive(true);
        $user->setOnline(false);
        $user->setPersonEmail($values->personEmail);
        $user->setPersonPhone($values->personPhone);
        $user->setPersonName($values->personName);
        $user->setHash(md5($values->personEmail));

        //Set roles
        switch ($values->role) {
            case 0:
                $this->role = $this->roleRepository->getByName('courier');
                break;
            case 1:
                $this->role = $this->roleRepository->getByName('coordinator');
                break;
            case 2:
                $this->role = $this->roleRepository->getByName('seamstress');
                break;
            case 3:
                $this->role = $this->roleRepository->getByName('operator');
                break;
            case 4:
                $this->role = $this->roleRepository->getByName('supplier');
                break;
            case 5:
                $this->role = $this->roleRepository->getByName('printman');
                break;
        }


        $user->setRole($this->role);

        if ($values->transport !== null) {
            /** @var Transport $transport */
            $transport = $this->transportRepository->find($values->transport ?? 7);
            $user->setTransport($transport);
        }

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

        if ($locationId != null) {
            /** @var Address $address */
            $address = new Address();
            if (isset($addr->city)) {
                $address->setCity($addr->city);
            }
            $address->setState($addr->state);
            $address->setLocationId($locationId);
            $address->setCountry($addr->country);
            if (isset($addr->county)) {
                $address->setDistrict($addr->county);
            }
            if (isset($addr->postalCode)) {
                $address->setPostalCode($addr->postalCode);
            }
            $address->setLongitude($gps->longitude);
            $address->setLatitude($gps->latitude);
        }

        $user->setAddress($address);


        $link = $this->getPresenter()->link('//Homepage:changePassword', $user->getHash());
        //@TODO-Add sending mail for medical person and for government user

        switch ($this->role->getName()) {
            case 'courier':
                $this->mail->sendCourierMail($values->personEmail, $link);
                break;
            case 'coordinator':
                $this->mail->sendCoordinatorMail($values->personEmail, $link);
                break;
            case 'operator':
                $this->mail->sendOperatorMail($values->personEmail, $link);
                break;
            case 'seamstress':
                $this->mail->sendSeamstressMail($values->personEmail, $link);
                break;
            case 'medicalCoordinator':
                $this->mail->sendMedicMail($values->personEmail, $link);
                break;
            case 'superuser':
                $this->mail->sendSuperuserMail($values->personEmail, $link);
                break;
        }

        try {
            $this->volunteerRepository->register($user);

            $this->getPresenter()->flashMessage($this->translator->translate('messages.registration.success'));
            $this->getPresenter()->redirect("Homepage:registrationFinished");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/add.latte');
        $this->template->role = $this->role;
        $this->template->render();
    }
}

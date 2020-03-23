<?php


namespace SousedskaPomoc\Components;


use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
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

    private $role;


    public function __construct(
        VolunteerRepository $volunteerRepository,
        Translator $translator,
        Passwords $passwords,
        TransportRepository $transport,
        Role $role,
        RoleRepository $roleRepository,
        Mail $mail,
        AddressRepository $addrRepository
    )
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->translator = $translator;
        $this->passwords = $passwords;
        $this->transportRepository = $transport;
        $this->role = $role;
        $this->roleRepository = $roleRepository;
        $this->mail = $mail;
        $this->addressRepository = $addrRepository;
    }



    public function createComponentRegisterVolunteerForm()
    {
        $form = new BootstrapForm;
//        $form->renderMode = RenderMode::VERTICAL_MODE;

        $form->addText('town', $this->translator->translate('forms.registerCoordinator.townLabel'))
            ->setPlaceholder('Ulice včetně čísla popisného')
            ->setRequired($this->translator->translate('forms.registerCoordinator.townRequired'));
        $form->addText('personName', $this->translator->translate('forms.registerCoordinator.nameLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.nameRequired'));
        $form->addText('personPhone', $this->translator->translate('forms.registerCoordinator.phoneLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.phoneRequired'));
        $form->addEmail('personEmail', $this->translator->translate('forms.registerCoordinator.mailLabel'))
            ->setRequired($this->translator->translate('forms.registerCoordinator.mailRequired'));
        $form->addHidden('locationId');

        if ($this->role == $this->roleRepository->getByName('courier') ) {
            $form->addSelect('car', $this->translator->translate('forms.registerCoordinator.carLabel'),
                $this->transportRepository->getAllAsArray())
                ->setRequired($this->translator->translate('forms.registerCoordinator.carRequired'));
        }

        $form->addSubmit('addVolunteerFormSubmit', $this->translator->translate('templates.profile.button'));
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
        $user->addRole($this->role);
        if ($this->role == $this->roleRepository->getByName('courier')) {
            $user->setTransport($this->transportRepository->getById($values->car));
        }

        $client = new \GuzzleHttp\Client();
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $client->get('https://geocoder.ls.hereapi.com/6.2/geocode.json?locationid='. $values->locationId . '&jsonattributes=1&gen=9&apiKey=Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q');
        $content = $response->getBody()->getContents();

        $content = json_decode($content);
        $addr= $content->response->view['0']->result['0']->location->address;

        $address = new Address();
        $address->setCity($addr->city);
        $address->setCountry($addr->country);
        $address->setDistrict($addr->district);
        $address->setLocationId($values->locationId);
        $address->setPostalCode($addr->postalCode);
        $address->setState($addr->state);
        $address->setStreet($addr->street);
        if (isset($addr->houseNumber)) {
            $address->setHouseNumber($addr->houseNumber);
        }



        $link = $this->getPresenter()->link('//Homepage:changePassword', $user->getHash());
        switch ($this->role->getName()){
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
        }

        try {
            $this->addressRepository->create($address);
            /** @var Address $dbAddress */
            $dbAddress = $this->addressRepository->getByLocationId($values->locationId);
            $user->setAddress($dbAddress);
            $this->volunteerRepository->register($user);
            $dbUser = $this->volunteerRepository->getByEmail($values->personEmail);
//            $this->addressRepository->updateVolunteer($values->locatinoId, $dbUser);
            $this->getPresenter()->flashMessage($this->translator->translate('messages.registration.success'));
            $this->getPresenter()->redirect("Homepage:registrationFinished");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    public function render()
    {
        $this->template->setFile(__DIR__.'/add.latte');
        $this->template->role = $this->role;
        $this->template->render();
    }

}
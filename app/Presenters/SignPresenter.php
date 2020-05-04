<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette\Http\FileUpload;
use Nette\Security\AuthenticationException;
use Nette\Utils\Image;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Forms;
use Nette\Application\UI\Form;
use SousedskaPomoc\Repository\VolunteerRepository;
use SousedskaPomoc\Components\IEditVolunteerFormInterface;
use SousedskaPomoc\Components\Suggester\ISuggesterTownInterface;
use SousedskaPomoc\Repository\AddressRepository;

final class SignPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink = '';

    /** @var Forms\SignInFormFactory */
    private $signInFactory;

    /** @var Forms\SignUpFormFactory */
    private $signUpFactory;

    /** @var VolunteerRepository */
    private $volunteerRepository;

    /** @var IEditVolunteerFormInterface */
    private $editVolunteerForm;

    /** @var ISuggesterTownInterface */
    private $townSuggester;

    /** @var AddressRepository */
    private $addressRepository;

    public function __construct(
        Forms\SignInFormFactory $signInFactory,
        Forms\SignUpFormFactory $signUpFactory,
        VolunteerRepository $volunteerRepository,
        IEditVolunteerFormInterface $editVolunteerForm,
        ISuggesterTownInterface $townSuggester,
        AddressRepository $addressRepository
    ) {
        $this->signInFactory = $signInFactory;
        $this->signUpFactory = $signUpFactory;
        $this->volunteerRepository = $volunteerRepository;
        $this->editVolunteerForm = $editVolunteerForm;
        $this->townSuggester = $townSuggester;
        $this->addressRepository = $addressRepository;

        parent::__construct();
    }


    /**
     * Sign-in form factory.
     */
    protected function createComponentSignInForm(): Form
    {
        return $this->signInFactory->create(function (): void {
            $this->restoreRequest($this->backlink);
            $this->redirect('Homepage:');
        });
    }


    /**
     * Sign-up form factory.
     */
    protected function createComponentSignUpForm(): Form
    {
        return $this->signUpFactory->create(function (): void {
            $this->redirect('Homepage:');
        });
    }


    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('Odhlášení proběhlo úspěšně');
        $this->redirect('Homepage:default');
    }

    public function renderCard()
    {
        if ($this->user->isLoggedIn()) {
            $this->template->volunteer = $this->volunteerRepository->getById($this->user->getId());
        } else {
            $this->flashMessage("Pro přístup ke kartičce se musíte přihlášit.");
            $this->redirect("Homepage:default");
        }
    }

    public function createComponentUserUploadPhoto()
    {
        $form = new Form();
        $form->addUpload('userPhoto', 'Fotografie k nahrání')->setRequired();
        $form->addSubmit('savePhoto', 'Nahrát fotografii');
        $form->onSuccess[] = [$this, "uploadUserPhoto"];
        return $form;
    }

    public function createComponentEditVolunteerForm()
    {
        $form = $this->editVolunteerForm->create();
        $form->onFinish[] = function () {
            $this->flashMessage($this->translator->translate('templates.profile.success'));
            $this->redirect("Sign:profile");
        };
        return $form;
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
            $this->redirect("Sign:profile");
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    public function createComponentTownSuggester()
    {
        return $this->townSuggester->create();
    }

    public function renderProfile()
    {
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Pro přístup do této sekce musíte být přihlášen(a).");
            $this->redirect("Sign:in");
        }
        $this->template->userDetails = $this->volunteerRepository->getById($this->user->getId());
    }

    public function uploadUserPhoto(Form $form)
    {
        $values = $form->getValues();
        $destinationPath = __DIR__ . '/../../www/upload/';

        /** @var FileUpload $file */
        $file = $values->userPhoto;

        if ($file->isOk() && $file->isImage()) {
            $file->move($destinationPath . $this->user->getId() . '_' . $file->getSanitizedName());
            $image = Image::fromFile($destinationPath . $this->user->getId() . '_' . $file->getSanitizedName());
            $image->resize(350, null);
            $image->save($destinationPath . 'card_' . $this->user->getId() . '_' . $file->getSanitizedName());
            $this->volunteerRepository->attachUserPhoto($this->user->getId(), $file->getSanitizedName());
            $this->flashMessage('Profilovou fotku jsme vám nahráli.');
            $this->redirect('PublicDemands:dashboard');
        } else {
            $this->flashMessage('Nahráli jste nepodporovaný typ souboru.');
            $this->redirect('this');
        }
    }
}

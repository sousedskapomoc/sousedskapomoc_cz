<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette\Http\FileUpload;
use Nette\Utils\Image;
use SousedskaPomoc\Forms;
use Nette\Application\UI\Form;
use SousedskaPomoc\Repository\VolunteerRepository;

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

    public function __construct(
        Forms\SignInFormFactory $signInFactory,
        Forms\SignUpFormFactory $signUpFactory,
        VolunteerRepository $volunteerRepository
    ) {
        $this->signInFactory = $signInFactory;
        $this->signUpFactory = $signUpFactory;
        $this->volunteerRepository = $volunteerRepository;

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
        $this->flashMessage( $this->translator->translate('messages.actionOut.logoutSuccessful') );
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
        $form->addUpload('userPhoto', $this->translator->translate('messages.componentUserUploadPhoto.photoForUpload') )->setRequired();
        $form->addSubmit('savePhoto', $this->translator->translate('messages.componentUserUploadPhoto.uploadPhoto') );
        $form->onSuccess[] = [$this, "uploadUserPhoto"];
        return $form;
    }

    public function renderProfile()
    {
        $this->template->volunteer = $this->volunteerRepository->getById($this->user->getId());
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
            $this->flashMessage( $this->translator->translate('messages.uploadUserPhoto.photoUploaded') );
            $this->redirect('PublicDemands:dashboard');
        } else {
            $this->flashMessage( $this->translator->translate('messages.uploadUserPhoto.unspportedFileType') );
            $this->redirect('this');
        }
    }
}

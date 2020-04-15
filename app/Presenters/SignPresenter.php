<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

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

    public function __construct(Forms\SignInFormFactory $signInFactory,
                                Forms\SignUpFormFactory $signUpFactory,
                                VolunteerRepository $volunteerRepository
    )
    {
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
        $this->getUser()->logout();
    }

    public function renderCard()
    {
        $this->template->volunteer = $this->volunteerRepository->getById($this->user->getId());
    }

    public function createComponentUserUploadPhoto()
    {
        $form = new Form();
        $form->addUpload('userPhoto', 'Fotografie k nahrání')->setRequired();
        $form->addSubmit('savePhoto', 'Nahrát fotografii');
        $form->onSuccess[] = [$this, "uploadUserPhoto"];
        return $form;
    }
}

<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;


use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Latte\Engine;
use Nette\Database\Connection;
use Nette\Mail\Message;
use Nette\Mail\Mailer;
use SousedskaPomoc\Model\UserManager;

final class HomepagePresenter extends BasePresenter
{
    /** @var \Nette\Database\Connection */
    protected $connection;

    /** @var \SousedskaPomoc\Model\UserManager */
    protected $userManager;

    /** @var Mailer */
    protected $mailer;

    public function beforeRender() {
        if($this->user->isLoggedIn()) {
            $this->redirect("System:dashboard");
        }
    }

    public function injectUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function injectMailer(Mailer $mailer) {
        $this->mailer = $mailer;
    }


    public function injectConnection(Connection $connection)
    {
        $this->connection = $connection;
    }



    public function createComponentRegisterAsCoordinator()
    {
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'coordinator');

        $form->addText('personName', 'Jméno a příjmení')
            ->setRequired('Potřebujeme vědět vaše jméno');
        $form->addText('personPhone', 'Telefon')
            ->setRequired('Potřebujeme vědět váš telefon abychom vám mohli zavolat');
        $form->addEmail('personEmail', 'E-mail')->setRequired('Potřebujeme znás váš e-mail kvůli registraci.');

        $form->addText('town', 'Město kde chci pomáhat')->setRequired('Potřebujeme vědět kde budete pomáhat');

        $form->addSubmit('coordinatorRegFormSubmit', 'Zaregistrovat se');
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }



    public function createComponentRegisterAsOperator()
    {
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'operator');

        $form->addText('personName', 'Jméno a příjmení')
            ->setRequired('Potřebujeme vědět vaše jméno');
        $form->addText('personPhone', 'Telefon')
            ->setRequired('Potřebujeme vědět váš telefon abychom vám mohli zavolat');
        $form->addEmail('personEmail', 'E-mail')->setRequired('Potřebujeme znás váš e-mail kvůli registraci.');

        $form->addText('town', 'Město kde chci pomáhat')->setRequired('Potřebujeme vědět kde budete pomáhat');

        $form->addSubmit('coordinatorRegFormSubmit', 'Zaregistrovat se');
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }



    public function createComponentRegisterAsCourier()
    {
        $cars = [
            1 => 'malý osobní automobil (Škoda Citigo, VW eUP, Toyota Aygo)',
            2 => 'vetší osobní automobil (Škoda Octavia, VW Passat)',
            3 => 'malý nákladní automobil (VW Caddy, Fiat Doblo)',
            4 => 'vetší nákladní automobil (Fiat Ducato, Mercedes Sprinter)',
        ];

        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->addHidden('role', 'courier');

        $form->addText('personName', 'Jméno a příjmení')
            ->setRequired('Potřebujeme vědět vaše jméno');
        $form->addText('personPhone', 'Telefon')
            ->setRequired('Potřebujeme vědět váš telefon abychom vám mohli zavolat');
        $form->addEmail('personEmail', 'E-mail')->setRequired('Potřebujeme znás váš e-mail kvůli registraci.');

        $form->addText('town', 'Město kde chci pomáhat')->setRequired('Potřebujeme vědět kde budete pomáhat');
        $form->addSelect('car', 'Auto které mám k dispozici', $cars)->setRequired('Potřebujeme vědět co uvezete');

        $form->addSubmit('coordinatorRegFormSubmit', 'Zaregistrovat se');
        $form->onSuccess[] = [$this, "processRegistration"];

        return $form;
    }

    public function sendRegistrationMail($to) {
        $mail = new Message;
        $mail->setFrom('info@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody('<h1>Děkujeme, že pomáháš s námi</h1><p>Tvá registrace proběhla úspěšně.</p>');

        $this->mailer->send($mail);
    }



    public function processRegistration(BootstrapForm $form)
    {
        $values = $form->getValues();
        if (!$this->userManager->check('personEmail', $values->personEmail)) {

            $this->userManager->register($values);
            $this->sendRegistrationMail($values->personEmail);
            $this->flashMessage("Vaše registrace proběhla úspěšně.");
            $this->redirect("RegistrationFinished");
        } else {
            $form->addError("Zadaný e-mail již existuje");
        }
    }
}

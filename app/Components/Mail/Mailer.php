<?php

namespace SousedskaPomoc\Components;

use Nette;
use Latte\Engine;
use SousedskaPomoc\Model\Email;
use SousedskaPomoc\Model\EmailManager;


final class Mail
{
    use Nette\SmartObject;

    /**
     * @var EmailManager
     */
    private $mailer;



    public function __construct(EmailManager $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendCourierMail($to, $link)
    {
        $latte = new Engine;
        $body = $latte->renderToString(__DIR__.'/courierMail.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Úspěšná registrace', $body);
        $this->mailer->send($mail);
    }


    public function sendSeamstressMail($to, $link)
    {
        $latte = new Engine;
        $body = $latte->renderToString(__DIR__.'/seamstressMail.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Úspěšná registrace', $body);
        $this->mailer->send($mail);
    }


    public function sendOperatorMail($to, $link)
    {
        $latte = new Engine;
		$body = $latte->renderToString(__DIR__.'/operatorMail.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Úspěšná registrace', $body);
        $this->mailer->send($mail);
    }


    public function sendCoordinatorMail($to, $link)
    {
        $latte = new Engine;
		$body = $latte->renderToString(__DIR__ . '/coordinatorMail.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Úspěšná registrace', $body);
        $this->mailer->send($mail);
    }


    public function sendLostPasswordMail($to, $link)
    {
        $latte = new Engine;
		$body = $latte->renderToString(__DIR__.'/lostPassword.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Zapomenuté heslo', $body);
        $this->mailer->send($mail);
    }


    public function sendSuperuserMail($to, $link)
    {
        $latte = new Engine;
        $body = $latte->renderToString(__DIR__.'/superuserMail.latte', ['url' => $link]);
		$mail = new Email($to, 'SousedskaPomoc.cz - Úspěšná registrace', $body);
        $this->mailer->send($mail);
    }

}

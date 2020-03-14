<?php

namespace SousedskaPomoc\Components;

use Nette;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Latte\Engine;


final class Mail
{
    use Nette\SmartObject;

    /**
     * @var Mailer
     */
    protected $mailer;



    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }



    public function sendMail($to, $title, $body)
    {
        $mail = new Message;
        $mail->setFrom('info@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject($title)
            ->setHtmlBody($body);

        $this->mailer->send($mail);
    }



    public function sendRegistrationMail($to)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('info@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/registrationMail.latte'));

        $this->mailer->send($mail);
    }
}

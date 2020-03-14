<?php

namespace SousedskaPomoc\Components;

use Nette;
use Nette\Mail\Mailer;
use Nette\Mail\Message;


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
        $mail->setFrom('info@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody('<h1>Děkujeme, že pomáháš s námi</h1><p>Tvá registrace proběhla úspěšně.</p>');

        $this->mailer->send($mail);
    }
}

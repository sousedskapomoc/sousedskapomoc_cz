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
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject($title)
            ->setHtmlBody($body);

        $this->mailer->send($mail);
    }



    public function sendCourierMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/courierMail.latte', ['url' => $link]));

        $this->mailer->send($mail);
    }



    public function sendSeamstressMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/seamstressMail.latte', ['url' => $link]));
        $this->mailer->send($mail);
    }



    public function sendOperatorMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/operatorMail.latte', ['url' => $link]));

        $this->mailer->send($mail);
    }



    public function sendCoordinatorMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/coordinatorMail.latte', ['url' => $link]));

        $this->mailer->send($mail);
    }



    public function sendLostPasswordMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Zapomenuté heslo')
            ->setHtmlBody($latte->renderToString(__DIR__.'/lostPassword.latte', ['url' => $link]));

        $this->mailer->send($mail);
    }

    public function sendSuperuserMail($to, $link)
    {
        $mail = new Message;
        $latte = new Engine;
        $mail->setFrom('robot@sousedskapomoc.cz')
            ->addTo($to)
            ->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
            ->setHtmlBody($latte->renderToString(__DIR__.'/superuserMail.latte', ['url' => $link]));

        $this->mailer->send($mail);
    }

	public function sendMedicMail($to, $link)
	{
		$mail = new Message;
		$latte = new Engine;
		$mail->setFrom('robot@sousedskapomoc.cz')
			->addTo($to)
//			->addAttachment('path/to/example.zip'); TODO - add path to attachment for doctors
			->setSubject('SousedskaPomoc.cz - Úspěšná registrace')
			->setHtmlBody($latte->renderToString(__DIR__.'/doctorMail.latte', ['url' => $link]));

		$this->mailer->send($mail);
	}
}

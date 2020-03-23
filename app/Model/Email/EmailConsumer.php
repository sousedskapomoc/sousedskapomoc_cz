<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;

use Nette\Mail\Mailer;
use Nette\Mail\Message;


class EmailConsumer extends Consumer
{

	/** @var string */
	private $fromMail;

	/** @var string */
	private $fromName;

	/** @var Mailer */
	private $mailer;


	public function __construct(Queue $queue, string $fromMail, string $fromName, Mailer $mailer)
	{
		parent::__construct($queue);
		$this->fromMail = $fromMail;
		$this->fromName = $fromName;
		$this->mailer = $mailer;
	}


	public function consumeSingle($email): ConsumeResult
	{
		assert($email instanceof Email);
		$emailMessage = (new Message())
			->setFrom($this->fromMail, $this->fromName)
			->addTo($email->getRecipient())
			->setSubject($email->getSubject())
			->setHtmlBody($email->getContent());
		$this->mailer->send($emailMessage);
		return new EmailConsumeResult($email);
	}

}

<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;

use Nette\SmartObject;


class Email
{
	use SmartObject;


	/** @var string */
	private $recipient;

	/** @var string */
	private $subject;

	/** @var string */
	private $content;


	public function __construct(string $recipient, string $subject, string $content)
	{
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->content = $content;
	}


	public function getRecipient(): string
	{
		return $this->recipient;
	}


	public function getSubject(): string
	{
		return $this->subject;
	}


	public function getContent(): string
	{
		return $this->content;
	}

}

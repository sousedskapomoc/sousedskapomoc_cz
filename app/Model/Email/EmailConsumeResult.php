<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;


class EmailConsumeResult implements ConsumeResult
{

	/** @var Email */
	private $email;


	public function __construct(Email $email)
	{
		$this->email = $email;
	}


	public function getDebugPrint(): string
	{
		return "Sent `{$this->email->getSubject()}` to `{$this->email->getRecipient()}`.";
	}

}

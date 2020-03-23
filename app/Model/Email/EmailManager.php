<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;

use Nette\SmartObject;


class EmailManager
{
	use SmartObject;


	/** @var Queue */
	private $queue;


	public function __construct(Queue $queue)
	{
		$this->queue = $queue;
	}


	public function send(Email $email): void
	{
		$this->queue->publish(serialize($email));
	}

}

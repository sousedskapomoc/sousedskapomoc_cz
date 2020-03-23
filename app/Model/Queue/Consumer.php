<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;


abstract class Consumer
{

	/** @var Queue */
	private $queue;


	public function __construct(Queue $queue)
	{
		$this->queue = $queue;
	}


	public function consumeAll(callable $onSuccess): void
	{
		$this->queue->consumeAll([$this, 'consumeSingle'], $onSuccess);
	}


	abstract public function consumeSingle($email): ConsumeResult;

}

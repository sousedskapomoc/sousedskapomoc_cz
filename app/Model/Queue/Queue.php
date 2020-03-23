<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;


interface Queue
{

	public function publish(string $content): void;

	public function consumeAll(callable $consumer, callable $onSuccess): void;

}

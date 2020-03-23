<?php declare(strict_types = 1);

namespace SousedskaPomoc\Model;

use Nette\SmartObject;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RabbitMqQueue implements Queue
{
	use SmartObject;


	/** @var string */
	private $routingKey;
	
	/** @var AMQPStreamConnection */
	private $connection;

	/** @var AMQPChannel  */
	private $channel;


	public function __construct(string $routingKey, AMQPStreamConnection $connection)
	{
		$this->routingKey = $routingKey;
		$this->connection = $connection;
		$this->channel = $connection->channel();
		$this->channel->queue_declare($routingKey, false, true, false, false);
	}


	public function publish(string $content): void
	{
		$message = new AMQPMessage($content, [
				'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
			]);
		$this->channel->basic_publish($message, '', $this->routingKey);
	}


	public function consumeAll(callable $consumer, callable $onSuccess): void
	{
		$this->channel->basic_consume(
			$this->routingKey,
			'',
			false,
			false,
			false,
			false,
			function (AMQPMessage $message) use ($consumer, $onSuccess) {
				$result = $consumer(unserialize($message->body));
				$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
				$onSuccess($result);
			});
		while ($this->channel->is_consuming()) {
			$this->channel->wait();
		}
	}


	public function __destruct()
	{
		$this->channel->close();
		$this->connection->close();
	}

}

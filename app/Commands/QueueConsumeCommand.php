<?php declare(strict_types = 1);

namespace SousedskaPomoc\Commands;

use Nette\DI\Container;
use SousedskaPomoc\Model\Consumer;
use SousedskaPomoc\Model\ConsumeResult;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class QueueConsumeCommand extends Command
{

	/** @var Container @inject */
	public $container;


	protected static $defaultName = 'queue:consume';


	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'Name of the queue to consume');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument('name');
		$consumer = $this->getConsumer($name);
		if (is_null($consumer)) {
			$output->writeln('<error>' . __CLASS__ . " | No consumer for queue `$name` found.</error>");
			return;
		}
		$output->writeln(__CLASS__ . " | Starting to consume queue `$name`.");
		$consumer->consumeAll(function (ConsumeResult $result) use ($output) {
			$output->writeln(__CLASS__ . ' | ' . $result->getDebugPrint());
		});
		$output->writeln(__CLASS__ . " | Done `$name`.");
	}


	private function getConsumer(string $name): ?Consumer
	{
		$services = $this->container->findByTag("consumer:$name");
		foreach ($services as $name => $_) {
			$service = $this->container->getService($name);
			if ($service instanceof Consumer) {
				return $service;
			}
		}
		return null;
	}

}

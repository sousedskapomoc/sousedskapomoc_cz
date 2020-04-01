<?php


namespace SousedskaPomoc\Commands;

use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\TransportRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDbCommand extends Command
{
	/** @var \SousedskaPomoc\Repository\VolunteerRepository */
	private $volunteerRepository;

	/** @var \SousedskaPomoc\Repository\RoleRepository */
	private $roleRepository;

	/** @var \SousedskaPomoc\Repository\TransportRepository */
	private $transportRepository;

	/** @var \SousedskaPomoc\Repository\AddressRepository */
	private $addressRepository;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(
		VolunteerRepository $volunteerRepository,
		Nette\Database\Context $database,
		RoleRepository $roleRepository,
		TransportRepository $transportRepository,
		AddressRepository $addressRepository
	)
	{
		parent::__construct();
		$this->database = $database;
		$this->volunteerRepository = $volunteerRepository;
		$this->roleRepository = $roleRepository;
		$this->transportRepository = $transportRepository;
		$this->addressRepository = $addressRepository;
	}

	public function configure()
	{
		$this->setName('migrate:old:db');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeLn('Starting migrating users');
		$users = $this->database->table('volunteers')->fetchAll();
		foreach ($users as $user) {
			/** @var Volunteer $newUser */
			$newUser = new Volunteer();
			$newUser->setOnline(0);
			// TODO - parse Role
			$newUser->setRole($this->roleRepository->getByName(''));
			$newUser->setTransport($this->transportRepository->getById($user['car']));
			$newUser->setPersonPhone($user['personPhone']);
			$newUser->setPersonEmail($user['personEmail']);
			$newUser->setPersonName($user['personName']);
			$newUser->setActive(1);

			//Parse user town and make an address from it
			$client = new \GuzzleHttp\Client();
			/** @var \GuzzleHttp\Psr7\Response $response */
			$response = $client->get('https://geocoder.ls.hereapi.com/6.2/geocode.json?city='. $user['town'] . '&jsonattributes=1&gen=9&apiKey=Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q');
			$content = $response->getBody()->getContents();

			$content = json_decode($content);
			$addr= $content->response->view['0']->result['0']->location->address;

			/** @var Address $address */
			$address = new Address();
			$address->setCity();
			$address->setState();
			$address->setLocationId();
			$address->setDistrict();
			$address->setCountry();

			$newUser->setAddress($address);
			$address->setVolunteer($newUser);
		}
	}
}

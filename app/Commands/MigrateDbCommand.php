<?php


namespace SousedskaPomoc\Command;

use Nette\Database\Context;
use Nette\Security\AuthenticationException;
use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Entities\Order;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\OrderRepository;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\TransportRepository;
use SousedskaPomoc\Repository\VolunteerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDbCommand extends Command
{
    /** @var VolunteerRepository */
    private $volunteerRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var TransportRepository */
    private $transportRepository;

    /** @var AddressRepository */
    private $addressRepository;

    /** @var OrderRepository */
    private $orderRepository;

    /** @var Context */
    private $database;

    public function __construct(
        VolunteerRepository $volunteerRepository,
        Context $database,
        RoleRepository $roleRepository,
        TransportRepository $transportRepository,
        AddressRepository $addressRepository,
        OrderRepository $orderRepository
    )
    {
        parent::__construct();
        $this->database = $database;
        $this->volunteerRepository = $volunteerRepository;
        $this->roleRepository = $roleRepository;
        $this->transportRepository = $transportRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
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
            $roles = explode(";", $user['role']);
            if (in_array("admin", $roles)) {
                $newUser->setRole($this->roleRepository->getByName('admin'));
            } else if (in_array("superuser", $roles)) {
                $newUser->setRole($this->roleRepository->getByName("superuser"));
            } else {
                $newUser->setRole($this->roleRepository->getByName($roles[0]));
            }
            if ($user['car'] != null) {
                $car = [
                    1 => 'Malé auto',
                    2 => 'Velké auto',
                    3 => 'Malá dodávka',
                    4 => 'Velká dodávka',
                    5 => 'Kolo',
                    6 => 'Motorka',
                    7 => 'Chůze'
                ];
                $newUser->setTransport($this->transportRepository->getByType($car[$user['car']]));
            }
            $newUser->setPersonPhone($user['personPhone']);
            $newUser->setPersonEmail($user['personEmail']);
            $newUser->setPersonName($user['personName']);
            $newUser->setActive(1);
            $newUser->setPassword($user['password']);
            $newUser->setHash($user['emailCode']);

            if ($user['town'] != NULL ) {
                //Parse user town and make an address from it
                $client = new \GuzzleHttp\Client();
                /** @var \GuzzleHttp\Psr7\Response $response */
                $response = $client->get('https://geocoder.ls.hereapi.com/6.2/geocode.json?city=' . $user['town'] . '&jsonattributes=1&gen=9&apiKey=Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q');
                $content = $response->getBody()->getContents();

                $content = json_decode($content);

                //array with address things
                $addr = $content->response->view['0']->result['0']->location->address;

                //HERE maps Id
                $locationId = $content->response->view['0']->result['0']->location->locationId;

                //array with latitude and longtitude
                $gps = $content->response->view['0']->result['0']->location->navigationPosition;

                /** @var Address $address */
                $address = new Address();
                $address->setCity($addr->city);
                $address->setState($addr->state);
                $address->setLocationId($locationId);
                $address->setCountry($addr->country);
                $address->setDistrict($addr->county);
                $address->setPostalCode($addr->postalCode);

//            $newUser->setAddress($address);
            $address->setVolunteer($newUser);
            }

            try {
                $this->addressRepository-
                ($address);
                $this->volunteerRepository->register($newUser);
            } catch (AuthenticationException $e) {
                continue;
            }
        }

//        //Migrate orders
//        $output->writeLn('Starting migrating orders');
//        $orders = $this->database->table('posted_orders')->fetchAll();
//        foreach ($orders as $o) {
//            /** @var Orders $order */
//            $order = new Orders();
//            $order->setItems($o['order_items']);
//            $order->setCustomerNote($o['note']);
//            $order->setCourierNote($o['courier_note']);
//            $order->setDeliveryPhone($o['delivery_phone']);
//            $order->setStatus($o['status']);
//            $courier = $this->volunteerRepository->getByEmail($o['courier_id']['personEmail']);
//            $order->setCourier($courier);
//            $usr = $this->volunteerRepository->getByEmail($o['id_volunteers']['personEmail']);
//            $order->setAuthor($usr);
//
//            //Parse user town and make an address from it
//            $client = new \GuzzleHttp\Client();
//            /** @var \GuzzleHttp\Psr7\Response $response */
//            $response = $client->get('https://geocoder.ls.hereapi.com/6.2/geocode.json?city='. $order['town'] . '&jsonattributes=1&gen=9&apiKey=Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q');
//            $content = $response->getBody()->getContents();
//
//            $content = json_decode($content);
//
//            //array with address things
//            $addr= $content->response->view['0']->result['0']->location->address;
//
//            //HERE maps Id
//            $locationId = $content->response->view['0']->result['0']->location->locationId;
//
//            //array with latitude and longtitude
//            $gps= $content->response->view['0']->result['0']->location->navigationPosition;
//
//            /** @var Address $address */
//            $address = new Address();
//            $address->setCity($addr->city);
//            $address->setState($addr->state);
//            $address->setLocationId($locationId);
//            $address->setCountry($addr->country);
//            $address->setDistrict($addr->county);
//            $address->setPostalCode($addr->postalCode);
//
//            $order->setDeliveryAddress($address);
//
//            $this->addressRepository->create($address);
//            $this->orderRepository->create($order);
//
//            $values['id'] = $order->getId();
//            $values['delivery_address'] = $o['delivery_address'];
//            $values['pickup_address'] = $o['pickup_address'];
//            $this->database->table('orders_address')->insert($values);
//        }
            return true;
        }
}
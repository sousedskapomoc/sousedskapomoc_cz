<?php

namespace SousedskaPomoc\Command;

use GuzzleHttp\Exception\ClientException;
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
use Symfony\Component\Console\Helper\ProgressBar;
use Tracy\Debugger;

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

    private $output;

    public function __construct(
        VolunteerRepository $volunteerRepository,
        Context $database,
        RoleRepository $roleRepository,
        TransportRepository $transportRepository,
        AddressRepository $addressRepository,
        OrderRepository $orderRepository
    ) {
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
        $this->output = $output;
        $this->migrateUsers();
        $this->migrateOrders();
        return true;
    }

    public function migrateUsers()
    {
        $this->output->writeLn('Starting migrating users');
        $users = $this->database->table('volunteers')->fetchAll();
        $progressBar = new ProgressBar($this->output, count($users));
        foreach ($users as $user) {
            if ($this->volunteerRepository->getByEmail($user['personEmail']) != null) {
                $progressBar->advance();
                continue;
            }
            /** @var Volunteer $newUser */
            $newUser = new Volunteer();
            $newUser->setOnline(0);
            // TODO - parse Role
            $roles = explode(";", $user['role']);
            if (in_array("admin", $roles)) {
                $newUser->setRole($this->roleRepository->getByName('admin'));
            } elseif (in_array("superuser", $roles)) {
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
                    7 => 'Chůze',
                ];
                $newUser->setTransport($this->transportRepository->getByType($car[$user['car']]));
            } else {
                $newUser->setTransport($this->transportRepository->getByType('Chůze'));
            }

            $newUser->setPersonPhone($user['personPhone']);
            $newUser->setPersonEmail($user['personEmail']);
            $newUser->setPersonName($user['personName']);
            $newUser->setActive(1);
            $newUser->setPassword($user['password']);
            if ($user['emailCode'] == null) {
                $user['emailCode'] = md5($user['personEmail']);
            }
            $newUser->setHash($user['emailCode']);

            if ($user['town'] != null) {
                //Parse user town and make an address from it
                $client = new \GuzzleHttp\Client();
                try {
                    /** @var \GuzzleHttp\Psr7\Response $response */
                    $basePath = 'https://geocoder.ls.hereapi.com/6.2/geocode.json?country=CZE&city=';
                    $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
                    $response = $client->get(
                        $basePath . $user['town'] . '&jsonattributes=1&gen=9&apiKey=' . $apiKey
                    );
                    $content = $response->getBody()->getContents();

                    $content = json_decode($content);

                    //array with address things
                    $addr = $content->response->view['0']->result['0']->location->address;

                    //HERE maps Id
                    $locationId = $content->response->view['0']->result['0']->location->locationId;

                    //array with latitude and longtitude
                    $gps = $content->response->view['0']->result['0']->location->navigationPosition;

                    if ($locationId != null) {
                        if (!$this->addressRepository->updateVolunteers($locationId, $newUser)) {
                            /** @var Address $address */
                            $address = new Address();
                            $address->setCity($addr->city);
                            $address->setState($addr->state);
                            $address->setLocationId($locationId);
                            $address->setCountry($addr->country);
                            $address->setDistrict($addr->county);
                            $address->setPostalCode($addr->postalCode);
                            $address->setLongitude($gps['0']->longitude);
                            $address->setLatitude($gps['0']->latitude);
                            $address->addVolunteer($newUser);
                            try {
                                $this->addressRepository->create($address);
                                $progressBar->advance();
                                continue;
                            } catch (AuthenticationException $e) {
                                Debugger::dump("User registration failed because ", $e->getMessage());
                            }
                        }
                    }
                } catch (ClientException $e) {
                    Debugger::dump("Bad response from HERE Maps, reason ", $e->getMessage());
                }
            }

            try {
                $this->volunteerRepository->register($newUser);
                $progressBar->advance();
            } catch (AuthenticationException $e) {
                Debugger::dump("User registration failed because ", $e->getMessage());
            }
        }
    }

    public function migrateOrders()
    {
        $this->output->writeLn('');
        $this->output->writeLn('Starting migrating orders');
        $orders = $this->database->table('posted_orders');
        $progressBar = new ProgressBar($this->output, count($orders));
        foreach ($orders as $o) {
            if ($this->orderRepository->getById($o['id']) != null) {
                $progressBar->advance();
                continue;
            }
            /** @var Order $order */
            $order = new Order();
            $order->setItems($o['order_items']);
            $order->setCustomerNote($o['note']);
            $order->setCourierNote($o['courier_note']);
            $order->setDeliveryPhone($o['delivery_phone']);
            $order->setStatus($o['status']);
            $order->setId($o['id']);

            if ($o['courier_id'] != null) {
                /** @var Volunteer $courier */
                $cour = $this->database->table('volunteers')->where('id', $o['courier_id'])->fetch();
                $courier = $this->volunteerRepository->getByEmail($cour['personEmail']);
                $courier->addDeliveredOrder($order);
                $this->volunteerRepository->save($courier);
            }

            /** @var Volunteer $usr */
            $us = $this->database->table('volunteers')->where('id', $o['id_volunteers'])->fetch();
            $usr = $this->volunteerRepository->getByEmail($us['personEmail']);
            $usr->addCreatedOrder($order);
            $this->volunteerRepository->save($usr);

            try {
                //Parse user town and make an address from it
                $client = new \GuzzleHttp\Client();
                /** @var \GuzzleHttp\Psr7\Response $response */
                $basePath = 'https://geocoder.ls.hereapi.com/6.2/geocode.json?country=CZE&city=';
                $apiKey = "Kl0wK4fx38Pf63EIey6WyrmGEhS2IqaVHkuzx0IQ4-Q";
                $response = $client->get($basePath . $o['town'] . '&jsonattributes=1&gen=9&apiKey=' . $apiKey);
                $content = $response->getBody()->getContents();

                $content = json_decode($content);

                //array with address things
                $addr = $content->response->view['0']->result['0']->location->address;

                //HERE maps Id
                $locationId = $content->response->view['0']->result['0']->location->locationId;

                //array with latitude and longtitude
                $gps = $content->response->view['0']->result['0']->location->navigationPosition;

                if ($locationId != null) {
                    /** @var Address $address */
                    $address = new Address();
                    $address->setCity($addr->city);
                    $address->setState($addr->state);
                    $address->setLocationId($locationId);
                    $address->setCountry($addr->country);
                    $address->setDistrict($addr->county);
                    $address->setPostalCode($addr->postalCode);
                    $address->setLongitude($gps['0']->longitude);
                    $address->setLatitude($gps['0']->latitude);
                    $address->addPickupOrder($order);
                    $address->addDeliveryOrder($order);
                }
            } catch (ClientException $e) {
                Debugger::dump("Bad response from HERE Maps, reason ", $e->getMessage());
            }

            try {
                $this->addressRepository->create($address);

                $values['id'] = $o['id'];
                $values['delivery_address'] = $o['delivery_address'];
                $values['pickup_address'] = $o['pickup_address'];
                $this->database->table('orders_address')->insert($values);
                $progressBar->advance();
            } catch (AuthenticationException $e) {
                Debugger::dump("Order not imported because ", $e->getMessage());
            }
        }
        $this->output->writeLn('');
    }
}

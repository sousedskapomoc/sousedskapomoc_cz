<?php

namespace SousedskaPomoc\Command;

use SousedskaPomoc\Entities\Address;
use SousedskaPomoc\Repository\AddressRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddressSyncCommand extends Command
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        parent::__construct();
        $this->addressRepository = $addressRepository;
    }

    public function configure()
    {
        $this->setName("address:data:sync")
            ->setDescription("");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Loading all addresses from DB");

        $addresses = $this->addressRepository->findAll();

        /** @var ProgressBar $progressBar */
        $progressBar = new ProgressBar($output, count($addresses));

        /** @var Address $address */
        foreach ($addresses as $address) {
            $output->writeln(
                [
                    $address->getLocationId(),
                    $address->getFullAddress()
                ]
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        return 1;
    }
}

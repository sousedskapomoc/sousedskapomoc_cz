<?php

namespace SousedskaPomoc\Command;

use SousedskaPomoc\Connectors\ConferenceCallBot;
use SousedskaPomoc\Entities\CallRoulette;
use SousedskaPomoc\Entities\TwilioWebhook;
use SousedskaPomoc\Repository\CallRouletteRepository;
use SousedskaPomoc\Repository\TwilioWebhookRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TwilioParserCommand extends Command
{
    /** @var TwilioWebhookRepository */
    protected $webhookRepository;

    /** @var CallRouletteRepository */
    protected $callRouletteRepository;
    /**
     * @var ConferenceCallBot
     */
    private $callBot;

    public function __construct(
        TwilioWebhookRepository $webhookRepository,
        CallRouletteRepository $callRouletteRepository,
        ConferenceCallBot $callBot
    ) {
        parent::__construct();

        $this->webhookRepository = $webhookRepository;
        $this->callRouletteRepository = $callRouletteRepository;
        $this->callBot = $callBot;
    }

    public function configure()
    {
        $this->setName('parse:twilio:webhooks');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $requests = $this->webhookRepository->findAllUnprocessed();

        $progressBar = new ProgressBar($output, count($requests));

        /** @var TwilioWebhook $request */
        foreach ($requests as $request) {
            parse_str($request->getRequest(), $fields);
            if (isset($fields['Digits'])) {
                $chatBot = new CallRoulette();
                $chatBot->setCallerPhone($fields['Caller']);
                $chatBot->setTopicId($fields['Digits']);
                $chatBot->setPaired(false);
                $chatBot->setNotes("Prichozi hovor na linku 800 400 299");

                $this->callRouletteRepository->store($chatBot);
            }

            $request->setProcessed(true);
            $this->webhookRepository->store($request);

            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln("Lets pair waiting callers");

        $this->callBot->connectCallers(
            $this->callRouletteRepository->findPairsForConference()
        );

        return true;
    }
}

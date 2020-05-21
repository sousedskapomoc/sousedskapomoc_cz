<?php

namespace SousedskaPomoc\Presenters;

use Nette\Application\UI\Presenter;
use SousedskaPomoc\Entities\CallRoulette;
use SousedskaPomoc\Entities\TwilioWebhook;
use SousedskaPomoc\Repository\CallRouletteRepository;
use SousedskaPomoc\Repository\TwilioWebhookRepository;

final class ApiPresenter extends Presenter
{
    /** @var CallRouletteRepository */
    protected $callRoulette;

    /** @var TwilioWebhookRepository */
    protected $twilioWebhook;

    public function injectCalRouletteRepository(CallRouletteRepository $callRouletteRepository)
    {
        $this->callRoulette = $callRouletteRepository;
    }

    public function injectTwilioWebhookRepository(TwilioWebhookRepository $twilioWebhookRepository)
    {
        $this->twilioWebhook = $twilioWebhookRepository;
    }

    public function renderCallRoulette()
    {
        $this->sendJson(
            [
                'waiting' => $this->callRoulette->findAllWaiting()
            ]
        );
    }

    public function renderCallTopics()
    {
        $callRoulette = new CallRoulette();
        $this->sendJson($callRoulette->getTopics());
    }

    public function renderStoreTwilioJson()
    {
        $json = file_get_contents('php://input') ?? null;

        $twilioWebhook = new TwilioWebhook();
        $twilioWebhook->setRequest($json);

        $this->twilioWebhook->store($twilioWebhook);

        if ($json !== null) {
            $twilioWebhook = json_decode($json);
        }

        $this->sendJson($twilioWebhook);
    }

    public function renderStartRoulette()
    {
        $callRoulette = new CallRoulette();
        $callRoulette->setCallerPhone($_GET['phone'] ?? null);
        $callRoulette->setTopicId($_GET['topic'] ?? 1);
        $callRoulette->setPaired(false);

        $this->callRoulette->store($callRoulette);

        $this->sendJson(
            $callRoulette->asArray()
        );
    }
}

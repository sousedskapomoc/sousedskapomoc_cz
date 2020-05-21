<?php

namespace SousedskaPomoc\Presenters;

use Nette\Application\UI\Presenter;
use SousedskaPomoc\Entities\CallRoulette;
use SousedskaPomoc\Repository\CallRouletteRepository;

final class ApiPresenter extends Presenter
{
    /** @var CallRouletteRepository */
    protected $callRoulette;

    public function injectCalRouletteRepository(CallRouletteRepository $callRouletteRepository)
    {
        $this->callRoulette = $callRouletteRepository;
    }

    public function renderCallRoulette()
    {
        $this->sendJson(
            [
                'waiting' => 0
            ]
        );
    }

    public function renderCallTopics()
    {
        $callRoulette = new CallRoulette();
        $this->sendJson($callRoulette->getTopics());
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

<?php

namespace SousedskaPomoc\Connectors;

use SousedskaPomoc\Repository\CallRouletteRepository;

class ConferenceCallBot
{
    /**
     * @var CallRouletteRepository
     */
    private $callRouletteRepository;
    /**
     * @var TwilioApi
     */
    private $twilioApi;

    /**
     * ConferenceCallBot constructor.
     * @param CallRouletteRepository $callRouletteRepository
     * @param TwilioApi $twilioApi
     */
    public function __construct(CallRouletteRepository $callRouletteRepository, TwilioApi $twilioApi)
    {
        $this->callRouletteRepository = $callRouletteRepository;
        $this->twilioApi = $twilioApi;
    }

    public function connectCallers($data)
    {
        $callsCount = 0;
        foreach ($data as $topicId => $callers) {
            if (count($callers) >= 2) {
                $title = "SP-{$topicId}-" . time();
                $this->twilioApi->setupConfCall($callers, $title);
                $callsCount++;
                foreach ($callers as $caller) {
                    $this->callRouletteRepository->markAsPaired($caller, $topicId);
                }
            }
        }

        return $callsCount;
    }
}

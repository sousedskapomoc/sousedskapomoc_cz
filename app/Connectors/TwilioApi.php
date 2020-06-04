<?php

namespace SousedskaPomoc\Connectors;

use Twilio\Rest\Client;

class TwilioApi
{
    const TOLL_FREE_NO = "+420800400299";

    /** @var Client */
    protected $client;

    public function __construct($clientId, $token)
    {
        $this->client = new Client($clientId, $token);
    }

    public function setupConfCall($callers, $conferenceCalId = null)
    {
        if ($conferenceCalId == null) {
            $time = time();
            $conferenceCalId = "SOUSEDSKYPOKEC-{$time}";
        }

        $twiml = "<Response>
                        <Play loop=\"1\">https://icee.cz/voice/conference-call.mp3</Play>                            
                        <Dial><Conference>{$conferenceCalId}</Conference></Dial>
                    </Response>";

        foreach ($callers as $caller) {
            $this->client->calls->create(
                $caller, // to
                self::TOLL_FREE_NO, // from
                [
                    "twiml" => $twiml
                ]
            );
        }
    }
}

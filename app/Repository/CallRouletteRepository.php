<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\CallRoulette;

class CallRouletteRepository extends DoctrineEntityRepository
{
    public function store(\SousedskaPomoc\Entities\CallRoulette $callRoulette)
    {
        $em = $this->getEntityManager();
        $em->persist($callRoulette);
        $em->flush();
    }

    public function findAllWaiting()
    {
        $output = [];

        /** @var CallRoulette $callRoulette */
        foreach ($this->findBy(['paired' => false]) as $callRoulette) {
            $output[] = ['phone' => $callRoulette->getCallerPhone(), 'topic' => $callRoulette->getTopicId()];
        }

        return $output;
    }

    public function getTopics()
    {
        $callRoulette = new CallRoulette();
        return $callRoulette->getTopics();
    }

    public function findPairsForConference()
    {
        $unpaired = $this->findBy(['paired' => 0]);

        $sortedByTopic = [];

        /** @var CallRoulette $callRoulette */
        foreach ($unpaired as $callRoulette) {
            $sortedByTopic[$callRoulette->getTopicId()][] = $callRoulette->getCallerPhone();
        }

        return $sortedByTopic;
    }

    public function markAsPaired($caller, int $topicId)
    {
        $results = $this->findBy(['callerPhone' => $caller, 'topicId' => $topicId]);

        /** @var CallRoulette $callRouletteEntry */
        foreach ($results as $callRoulleteEntry) {
            $callRouletteEntry->setPaired(true);
            $this->store($callRouletteEntry);
        }
    }
}

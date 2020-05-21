<?php

namespace SousedskaPomoc\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use SousedskaPomoc\Entities\TwilioWebhook;

class TwilioWebhookRepository extends DoctrineEntityRepository
{
    public function store(TwilioWebhook $twilioWebhook)
    {
        $em = $this->getEntityManager();
        $em->persist($twilioWebhook);
        $em->flush();
    }
}

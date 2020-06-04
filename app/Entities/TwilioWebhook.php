<?php


namespace SousedskaPomoc\Entities;

use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CallRoulette
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\TwilioWebhookRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TwilioWebhook
{
    use Id;

    use Timestampable;

    /**
     * @ORM\Column(type="text")
     */
    protected $request;

    /**
     * @ORM\Column(type="boolean", options="{default: false}")
     */
    protected $processed = false;

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     */
    public function setProcessed(bool $processed): void
    {
        $this->processed = $processed;
    }
}

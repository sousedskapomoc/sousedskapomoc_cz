<?php

namespace SousedskaPomoc\Entities;

use Nettrine\ORM\Entity\Attributes\Id;
use SousedskaPomoc\Entities\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CallRoulette
 * @ORM\Entity(repositoryClass="SousedskaPomoc\Repository\CallRouletteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CallRoulette
{
    use Id;

    use Timestampable;

    /**
     * @ORM\Column(type="string")
     */
    protected $callerPhone;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $topicId;

    /**
     * @ORM\Column(type="string")
     */
    protected $paired;

    /**
     * @return mixed
     */
    public function getCallerPhone()
    {
        return $this->callerPhone;
    }

    /**
     * @param mixed $callerPhone
     */
    public function setCallerPhone($callerPhone): void
    {
        $this->callerPhone = $callerPhone;
    }

    /**
     * @return mixed
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * @param mixed $topicId
     */
    public function setTopicId($topicId): void
    {
        $this->topicId = $topicId;
    }

    /**
     * @return mixed
     */
    public function getPaired()
    {
        return $this->paired;
    }

    /**
     * @param mixed $paired
     */
    public function setPaired($paired): void
    {
        $this->paired = $paired;
    }

    public function asArray()
    {
        return [
            'id' => $this->getId(),
            'phone' => $this->getCallerPhone(),
            'topic' => $this->getTopicId(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt()
        ];
    }
}

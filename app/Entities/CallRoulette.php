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

    protected $topics = [
        1 => 'Sex',
        2 => 'Historie / Dějiny Umění',
        3 => 'Sport',
        4 => 'Gastronomie, Jídlo a vaření',
        5 => 'Zdravotní stav',
        6 => 'COVID 19',
        7 => 'Internet - novodobé technologie',
        8 => 'Zahradnictví, kutilství a ostatní koníčci',
        9 => 'Literature'
    ];

    /**
     * @ORM\Column(type="string")
     */
    protected $callerPhone;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $topicId;

    /**
     * @ORM\Column(type="boolean",options="{default: 0}")
     */
    protected $paired = false;

    /**
     * @ORM\Column(type="text")
     */
    protected $notes;

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

    /**
     * @return string[]
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes): void
    {
        $this->notes = $notes;
    }
}

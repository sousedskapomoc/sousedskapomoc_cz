<?php

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Components\ICreateTownEventFormInterface;

final class EventSystemPresenter extends BasePresenter
{
    /** @var ICreateTownEventFormInterface */
    protected $createTownEventForm;

    public function injectCreateTownEventForm(ICreateTownEventFormInterface $eventForm)
    {
        $this->createTownEventForm = $eventForm;
    }

    public function createComponentCreateTownEvent()
    {
        return $this->createTownEventForm->create();
    }
}

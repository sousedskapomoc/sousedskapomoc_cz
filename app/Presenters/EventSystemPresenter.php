<?php

namespace SousedskaPomoc\Presenters;

use SousedskaPomoc\Components\ICreateTownEventFormInterface;
use SousedskaPomoc\Components\ICreateTownNotificationForm;

final class EventSystemPresenter extends BasePresenter
{
    /** @var ICreateTownEventFormInterface */
    protected $createTownEventForm;

    /** @var ICreateTownNotificationForm */
    protected $createTownNotificationForm;

    public function injectCreateTownEventForm(ICreateTownEventFormInterface $eventForm)
    {
        $this->createTownEventForm = $eventForm;
    }

    public function injectCreateTownNotificationForm(ICreateTownNotificationForm $notificationForm)
    {
        $this->createTownNotificationForm = $notificationForm;
    }

    public function createComponentCreateTownNotification()
    {
        return $this->createTownNotificationForm->create();
    }

    public function createComponentCreateTownEvent()
    {
        return $this->createTownEventForm->create();
    }
}

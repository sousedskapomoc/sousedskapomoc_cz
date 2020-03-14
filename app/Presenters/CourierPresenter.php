<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

final class CourierPresenter extends BasePresenter
{
    public function handleToggleActive($active) {
        $this->template->userActive = $active;
    }
}

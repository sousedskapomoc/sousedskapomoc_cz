<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

final class SystemPresenter extends BasePresenter
{
    public function renderDashboard()
    {
        $this->template->statistics = [
            'totalCount' => $this->userManager->fetchTotalCount(),
            'couriersCount' => $this->userManager->fetchCountBy(['role' => 'courier']),
            'operatorsCount' => $this->userManager->fetchCountBy(['role' => 'operator']),
            'coordinatorsCount' => $this->userManager->fetchCountBy(['role' => 'coordinator']),
            'usersWithoutAccess' => $this->userManager->fetchCountBy(['password' => null]),
            'uniqueTowns' => $this->userManager->fetchUniqueTownsCount(),
            'ordersCount' => $this->orderManager->fetchCount(),
        ];
    }
}
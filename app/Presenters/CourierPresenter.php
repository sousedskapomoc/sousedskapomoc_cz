<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

final class CourierPresenter extends BasePresenter
{
	public function renderDashboard()
	{
		$user = $this->userManager->isOnline($this->user->getId());
		$this->template->userOnline = $user->active;
	}

	public function handleToggleActive($active)
	{
		$this->userManager->setOnline($this->user->getId(), $active);
		$this->flashMessage("Změna stavu byla nastavena.");
		$this->redirect('this');
	}
}

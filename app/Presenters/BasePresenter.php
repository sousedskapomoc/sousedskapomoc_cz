<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Model\UserManager;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @persistent */
	public $locale;

	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	/** @var UserManager */
	protected $userManager;

	/** @var OrderManager */
	protected $orderManager;


	public function injectOrderManager(OrderManager $orderManager)
	{
		$this->orderManager = $orderManager;
	}


	public function injectUserManager(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}


	public function beforeRender()
	{
		if ($this->user->isLoggedIn()) {
			$town = $this->user->getIdentity()->data['town'];
			$this->template->availableCouriers = $this->userManager->fetchAvailableCouriersInTown($town);
		}

		$this->template->addFilter('getTranslation', function ($string) {
			return $this->translator->trans($string);
		});

		$this->template->addFilter('fetchUser', function ($courierId) {
			return $this->userManager->fetchCourierName($courierId);
		});

		$this->template->addFilter('logic', function ($state) {
			$states = [
				0 => 'Vypnuto',
				1 => 'Zapnuto'
			];

			return $states[$state] ?? $state[0];
		});

		$this->template->addFilter('humanFriendlyStatus', function ($status) {

			$statusList = [
				'new' => $this->translator->translate('templates.order.statusNew'),
				'assigned' => $this->translator->translate('templates.order.statusAssigned'),
				'picking' => $this->translator->translate('templates.order.statusPicking'),
				'delivering' => $this->translator->translate('templates.order.statusDelivering'),
				'delivered' => $this->translator->translate('templates.order.statusDelivered'),
			];

			return $statusList[$status] ?? $status;
		});
	}
}

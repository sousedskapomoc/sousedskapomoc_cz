<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Kdyby\Translation\Translator;
use Nette;
use SousedskaPomoc\Components\Suggester\ISuggesterAddressInterface;
use SousedskaPomoc\Components\Suggester\ISuggesterTownInterface;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Model\UserManager;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @persistent */
    public $locale;

    /** @var Translator @inject */
    public $translator;

    /** @var UserManager */
    protected $userManager;

    /** @var OrderManager */
    protected $orderManager;

    /** @var ISuggesterTownInterface */
    protected $suggesterTown;

    /** @var ISuggesterAddressInterface */
    protected $suggesterAddress;

    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }


    public function injectUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function injectTownSuggester(ISuggesterTownInterface $suggesterTown)
    {
        $this->suggesterTown = $suggesterTown;
    }

    public function injectAddressSuggester(ISuggesterAddressInterface $suggesterAddress)
    {
        $this->suggesterAddress = $suggesterAddress;
    }

    public function createComponentSuggesterAddress()
    {
        return $this->suggesterAddress->create();
    }

    public function createComponentSuggesterTown()
    {
        return $this->suggesterTown->create();
    }

    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $town = $this->userManager->getTownForUser($this->user->getId());
            $this->template->town = $town;

            if (($town == null || $town == "") && $this->presenter->view != "enterTown") {
                $this->redirect("System:enterTown");
            }
            $this->template->availableCouriers = $this->userManager->fetchAvailableCouriersInTown($town);
        }

        $this->template->addFilter('getTranslation', function ($string) {
            return $this->translator->trans($string);
        });

        $this->template->addFilter('fetchUser', function ($courierId) {
            return $this->userManager->fetchCourierName($courierId);
        });

        $this->template->addFilter('fetchPhone', function ($courierId) {
            return $this->userManager->fetchPhoneNumber($courierId);
        });

        $this->template->addFilter('fetchCar', function ($courierId) {
            $car = [
                1 => $this->translator->translate('templates.vehicleType.smallCar'),
                2 => $this->translator->translate('templates.vehicleType.bigCar'),
                3 => $this->translator->translate('templates.vehicleType.smallVan'),
                4 => $this->translator->translate('templates.vehicleType.bigVan'),
                5 => $this->translator->translate('templates.vehicleType.bicycle'),
                6 => $this->translator->translate('templates.vehicleType.motorbike'),
                7 => $this->translator->translate('templates.vehicleType.walk')
            ];

            return $car[$courierId] ?? 'neuveden';
        });

        $this->template->addFilter('logic', function ($state) {
            $states = [
                0 => $this->translator->translate('messeges.status.disabled'),
                1 => $this->translator->translate('messeges.status.enabled')
            ];

            return $states[$state] ?? $state[0];
        });

        $this->template->addFilter('createList', function ($items) {
            $output = null;
            foreach (explode(",", $items) as $item) {
                $output .= "<li>$item</li>";
            }
            return $output;
        });

        $this->template->addFilter('humanFriendlyStatus', function ($status) {

            $statusList = [
                'new' => $this->translator->translate('templates.order.statusNew'),
                'assigned' => $this->translator->translate('templates.order.statusAssigned'),
                'picking' => $this->translator->translate('templates.order.statusPicking'),
                'delivering' => $this->translator->translate('templates.order.statusDelivering'),
                'delivered' => $this->translator->translate('templates.order.statusDelivered'),
                'waiting' => $this->translator->translate('templates.order.statusWaiting'),
            ];

            return $statusList[$status] ?? $status;
        });
    }

    public function handleUpdateOrderStatus($orderId, $orderStatus)
    {
        $orderStatus = $_POST['orderStatus'] ?? $orderStatus;
        $this->orderManager->updateStatus($orderId, $orderStatus);
        $this->flashMessage($this->translator->translate('messages.order.statusChanged'));
        $this->redirect('this');
    }

    public function handleUpdateTown($orderId, $town)
    {
        $town = $_POST['town'] ?? $town;
        $this->orderManager->updateTown($orderId, $town);
        $this->flashMessage($this->translator->translate('messages.order.townChanged'));
        $this->redirect('this');
    }
}

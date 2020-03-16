<?php

declare(strict_types=1);

namespace SousedskaPomoc\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
        $router = new RouteList;
        $router->addRoute('/change-password', 'Homepage:changePassword');
        $router->addRoute('[<locale=cs cs|en>/]<presenter>/<action>', 'Homepage:default');

        return $router;
    }
}

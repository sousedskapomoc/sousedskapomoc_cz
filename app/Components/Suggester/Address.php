<?php

namespace SousedskaPomoc\Components\Suggester;

use Nette\Application\UI\Control;

class Address extends Control
{
    public function render()
    {
        $this->template->setFile(__DIR__ . '/address.latte');
        $this->template->render();
    }
}

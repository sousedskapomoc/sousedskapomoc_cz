<?php

namespace SousedskaPomoc\Components\Suggester;

use Nette\Application\UI\Control;

class Town extends Control
{
    public function render()
    {
        $this->template->setFile(__DIR__ . '/town.latte');
        $this->template->render();
    }
}
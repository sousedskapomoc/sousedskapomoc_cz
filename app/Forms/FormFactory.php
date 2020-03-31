<?php

declare(strict_types=1);

namespace SousedskaPomoc\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette;
use Nette\Application\UI\Form;

final class FormFactory
{
    use Nette\SmartObject;

    public function create(): Form
    {
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::VERTICAL_MODE;
        return $form;
    }
}

<?php

namespace SousedskaPomoc\Components;

use SousedskaPomoc\Components\DemandFormControl;

interface IDemandFormInterface
{
    /** @return DemandFormControl */
    public function create();
}

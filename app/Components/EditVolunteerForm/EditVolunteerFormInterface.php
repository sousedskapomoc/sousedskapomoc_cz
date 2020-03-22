<?php


namespace SousedskaPomoc\Components;

use SousedskaPomoc\Components\EditVolunteerFormControl;

interface IEditVolunteerFormInterface
{
    /** @return EditVolunteerFormControl */
    public function create();
}
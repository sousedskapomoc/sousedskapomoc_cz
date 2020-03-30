<?php


namespace SousedskaPomoc\Components;

use SousedskaPomoc\Components\EditVolunteerFormControl;
use SousedskaPomoc\Entities\Role;

interface IRegisterVolunteerFormInterface
{
    /** @return RegisterVolunteerFormControl */
    public function create(Role $role);
}
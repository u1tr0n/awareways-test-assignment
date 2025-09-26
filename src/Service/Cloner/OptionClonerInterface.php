<?php

namespace App\Service\Cloner;

use App\Entity\Option;

interface OptionClonerInterface
{
    public function clone(Option $option): Option;
}

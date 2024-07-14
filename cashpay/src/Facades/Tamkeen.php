<?php

namespace Techsoft\Cashpay\Facades;

use Illuminate\Support\Facades\Facade;

class Tamkeen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Techsoft\Cashpay\Tamkeen::class;
    }
}

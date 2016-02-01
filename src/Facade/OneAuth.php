<?php

namespace Hilabs\OneAuth\Facade;

use Illuminate\Support\Facades\Facade;

class OneAuth extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'one-auth'; }
}

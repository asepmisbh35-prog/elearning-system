<?php

return array_filter([
    App\Providers\AppServiceProvider::class,
    class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)
        ? App\Providers\TelescopeServiceProvider::class
        : null,
]);

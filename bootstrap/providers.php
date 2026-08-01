<?php

return [
    App\Providers\AppServiceProvider::class,
    Laravel\Fortify\FortifyServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\PaymentServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];

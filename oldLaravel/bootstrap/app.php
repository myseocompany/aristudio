<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

/*
|--------------------------------------------------------------------------
| PHP 8+ deprecation noise
|--------------------------------------------------------------------------
|
| Laravel 5.4 está pensado para PHP 7.x. En PHP 8.x los avisos deprecados
| por firmas de interfaces (ArrayAccess, Countable, etc.) se convierten en
| excepciones. Silenciamos E_DEPRECATED para que la app pueda ejecutarse
| en entornos modernos sin romper.
|
*/
if (PHP_VERSION_ID >= 80000) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

//echo realpath(__DIR__.'/../');
//exit();
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

//$app->useStoragePath(__DIR__.'/storage');

/*
$app = new App\MyApp(
    [
        'base' => realpath(__DIR__.'/../'),
        'public' => realpath(__DIR__.'/../../public')
    ]
);
*/

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

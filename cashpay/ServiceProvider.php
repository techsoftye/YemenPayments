<?php

namespace Techsoft\Cashpay;

use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cashpay.php', 'cashpay');

        $this->app->singleton(Tamkeen::class, function ($app) {
            return new Tamkeen(
                config('cashpay.username'),
                config('cashpay.password'),
                config('cashpay.service_provider_id'),
                config('cashpay.encryption_key'),
                storage_path('app/' . config('cashpay.certificate_path')),
                config('cashpay.certificate_password')
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cashpay.php' => config_path('cashpay.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../routes/yemenpayment.php' => base_path('routes/yemenpayment.php'),
        ], 'routes');

        $this->publishes([
            __DIR__.'/../stubs/CashPayController.php' => app_path('Http/Controllers/Payment/CashPayController.php'),
            __DIR__.'/../stubs/FloosakController.php' => app_path('Http/Controllers/Payment/FloosakController.php'),
            __DIR__.'/../stubs/JawaliController.php' => app_path('Http/Controllers/Payment/JawaliController.php'),
        ], 'controllers');

        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }
    }
}

<?php

namespace App\Providers;

use App\Domain\Communication\Adapters\AgoraRTCProviderAdapter;
use App\Domain\Communication\Contracts\CommunicationProviderInterface;
use App\Domain\Payment\Adapters\SandboxedYERPaymentAdapter;
use App\Domain\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Recommendation\Contracts\MedicalAssistantProviderInterface;
use App\Domains\Recommendation\Providers\RuleBasedAssistantProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            CommunicationProviderInterface::class,
            AgoraRTCProviderAdapter::class
        );

        $this->app->singleton(
            PaymentGatewayInterface::class,
            SandboxedYERPaymentAdapter::class
        );

        $this->app->singleton(
            MedicalAssistantProviderInterface::class,
            RuleBasedAssistantProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

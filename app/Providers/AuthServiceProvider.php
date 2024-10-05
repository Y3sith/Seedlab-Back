<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Configurar expiración de tokens
        Passport::tokensExpireIn(Carbon::now()->addHours(12)); // Tokens de acceso expiran en 12 horas
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30)); // Tokens de actualización expiran en 30 días
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMonths(6)); // Tokens de acceso personal expiran en 6 meses
    }
}

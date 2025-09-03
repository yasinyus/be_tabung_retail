<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Tabung::class => \App\Policies\TabungPolicy::class,
        \App\Models\TabungActivity::class => \App\Policies\TabungActivityPolicy::class,
        \App\Models\Armada::class => \App\Policies\ArmadaPolicy::class,
        \App\Models\Pelanggan::class => \App\Policies\PelangganPolicy::class,
        \App\Models\Gudang::class => \App\Policies\GudangPolicy::class,
        \App\Models\Transaction::class => \App\Policies\TransactionPolicy::class,
        \App\Models\Deposit::class => \App\Policies\DepositPolicy::class,
        \App\Models\Audit::class => \App\Policies\AuditPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

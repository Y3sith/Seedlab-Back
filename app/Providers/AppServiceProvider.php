<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Fluent;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('longBinary', function($column) {
            return $this->addColumn('longBinary', $column);
        });

        MySqlGrammar::macro('typeLongBinary', function (Fluent $column) {
            return 'longblob'; // Tipo equivalente en MySQL
        });
    }
}

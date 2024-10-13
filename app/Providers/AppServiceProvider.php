<?php

namespace App\Providers;

use App\Repositories\Aliado\AliadoRepository;
use App\Repositories\Aliado\AliadoRepositoryInterface;
use App\Repositories\Asesorias\AsesoriaRepository;
use App\Repositories\Asesorias\AsesoriaRepositoryInterface;
use App\Repositories\Banner\BannerRepository;
use App\Repositories\Banner\BannerRepositoryInterface;
use App\Repositories\ContenidoLeccion\ContenidoLeccionRepository;
use App\Repositories\ContenidoLeccion\ContenidoLeccionRepositoryInterface;
use App\Repositories\Dashboard\DashboardRepository;
use App\Repositories\Dashboard\DashboardRepositoryInterface;
use App\Repositories\Ubicacion\UbicacionRepository;
use App\Repositories\Ubicacion\UbicacionRepositoryInterface;
use App\Services\AliadoService;
use App\Services\AsesoriaService;
use App\Services\BannerService;
use App\Services\ContenidoLeccionService;
use App\Services\ImageService;
use App\Services\UbicacionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Fluent;
use Laravel\Passport\Passport;
use Carbon\Carbon;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class,DashboardRepository::class);
        $this->app->bind(AliadoRepositoryInterface::class, AliadoRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(AsesoriaRepositoryInterface::class, AsesoriaRepository::class);
        $this->app->bind(UbicacionRepositoryInterface::class, UbicacionRepository::class);
        $this->app->bind(ContenidoLeccionRepositoryInterface::class, ContenidoLeccionRepository::class);

        

        // Registro de Servicios
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        $this->app->singleton(AliadoService::class, function ($app) {
            return new AliadoService(
                $app->make(AliadoRepositoryInterface::class),
                $app->make(ImageService::class)
            );
        });

        $this->app->singleton(BannerService::class, function ($app) {
            return new BannerService(
                $app->make(BannerRepositoryInterface::class),
                $app->make(ImageService::class)
            );
        });

        $this->app->singleton(AsesoriaService::class, function ($app) {
            return new AsesoriaService(
                $app->make(AsesoriaRepositoryInterface::class)
            );
        });
    
        $this->app->singleton(UbicacionService::class, function ($app) {
            return new UbicacionService($app->make(UbicacionRepositoryInterface::class));
        });

        $this->app->singleton(ContenidoLeccionService::class, function ($app) {
            return new ContenidoLeccionService(
                $app->make(ContenidoLeccionRepositoryInterface::class),
            $app->make(ImageService::class));
        });
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

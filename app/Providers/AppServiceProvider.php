<?php

namespace App\Providers;

use App\Repositories\Actividad\ActividadRepository;
use App\Repositories\Actividad\ActividadRepositoryInterface;
use App\Repositories\Aliado\AliadoRepository;
use App\Repositories\Aliado\AliadoRepositoryInterface;
use App\Repositories\Apoyo\ApoyoRepository;
use App\Repositories\Apoyo\ApoyoRepositoryInterface;
use App\Repositories\Asesor\AsesorRepository;
use App\Repositories\Asesor\AsesorRepositoryInterface;
use App\Repositories\Asesorias\AsesoriaRepository;
use App\Repositories\Asesorias\AsesoriaRepositoryInterface;
use App\Repositories\Banner\BannerRepository;
use App\Repositories\Banner\BannerRepositoryInterface;
use App\Repositories\ContenidoLeccion\ContenidoLeccionRepository;
use App\Repositories\ContenidoLeccion\ContenidoLeccionRepositoryInterface;
use App\Repositories\Dashboard\DashboardRepository;
use App\Repositories\Dashboard\DashboardRepositoryInterface;
use App\Repositories\Emprendedor\EmprendedorRepository;
use App\Repositories\Emprendedor\EmprendedorRepositoryInterface;
use App\Repositories\Empresa\EmpresaRepository;
use App\Repositories\Empresa\EmpresaRepositoryInterface;
use App\Repositories\Leccion\LeccionRepository;
use App\Repositories\Leccion\LeccionRepositoryInterface;
use App\Repositories\Nivel\NivelRepository;
use App\Repositories\Nivel\NivelRepositoryInterface;
use App\Repositories\Orientador\OrientadorRepository;
use App\Repositories\Orientador\OrientadorRepositoryInterface;
use App\Repositories\Ruta\RutaRepository;
use App\Repositories\Ruta\RutaRepositoryInterface;
use App\Repositories\SuperAdmin\SuperAdminRepository;
use App\Repositories\SuperAdmin\SuperAdminRepositoryInterface;
use App\Repositories\Ubicacion\UbicacionRepository;
use App\Repositories\Ubicacion\UbicacionRepositoryInterface;
use App\Services\ActividadService;
use App\Services\AliadoService;
use App\Services\ApoyoService;
use App\Services\AsesoriaService;
use App\Services\AsesorService;
use App\Services\BannerService;
use App\Services\ContenidoLeccionService;
use App\Services\EmprendedorService;
use App\Services\EmpresaService;
use App\Services\ImageService;
use App\Services\LeccionService;
use App\Services\NivelService;
use App\Services\OrientadorService;
use App\Services\RutaService;
use App\Services\SuperAdminService;
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
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(AliadoRepositoryInterface::class, AliadoRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(AsesoriaRepositoryInterface::class, AsesoriaRepository::class);
        $this->app->bind(UbicacionRepositoryInterface::class, UbicacionRepository::class);
        $this->app->bind(ContenidoLeccionRepositoryInterface::class, ContenidoLeccionRepository::class);
        $this->app->bind(AsesorRepositoryInterface::class, AsesorRepository::class);
        $this->app->bind(EmprendedorRepositoryInterface::class, EmprendedorRepository::class);
        $this->app->bind(ApoyoRepositoryInterface::class, ApoyoRepository::class);
        $this->app->bind(EmpresaRepositoryInterface::class, EmpresaRepository::class);
        $this->app->bind(NivelRepositoryInterface::class, NivelRepository::class);
        $this->app->bind(LeccionRepositoryInterface::class, LeccionRepository::class);
        $this->app->bind(ActividadRepositoryInterface::class, ActividadRepository::class);
        $this->app->bind(OrientadorRepositoryInterface::class, OrientadorRepository::class);
        $this->app->bind(SuperAdminRepositoryInterface::class, SuperAdminRepository::class);
        $this->app->bind(RutaRepositoryInterface::class, RutaRepository::class);




        // Registro de Servicios
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        $this->app->singleton(AliadoService::class, function ($app) {
            return new AliadoService(
                $app->make(AliadoRepositoryInterface::class),
                $app->make(ImageService::class),
                $app->make(BannerRepositoryInterface::class)
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
                $app->make(ImageService::class)
            );
        });

        $this->app->singleton(AsesorService::class, function ($app) {
            return new AsesorService($app->make(AsesorRepositoryInterface::class));
        });

        $this->app->singleton(EmprendedorService::class, function ($app) {
            return new EmprendedorService($app->make(EmprendedorRepositoryInterface::class));
        });

        $this->app->singleton(ApoyoService::class, function ($app) {
            return new ApoyoService($app->make(ApoyoRepositoryInterface::class));
        });

        $this->app->singleton(EmpresaService::class, function ($app) {
            return new EmpresaService($app->make(EmpresaRepositoryInterface::class));
        });

        $this->app->singleton(NivelService::class, function ($app) {
            return new NivelService(
                $app->make(NivelRepositoryInterface::class),
                $app->make(ActividadRepositoryInterface::class),
                $app->make(AsesorRepositoryInterface::class)
            );
        });

        $this->app->singleton(LeccionService::class, function ($app) {
            return new LeccionService($app->make(LeccionRepositoryInterface::class));
        });

        $this->app->singleton(ActividadService::class, function ($app) {
            return new ActividadService(
                $app->make(ActividadRepositoryInterface::class),
                $app->make(AliadoRepositoryInterface::class),
                $app->make(ImageService::class)
            );
        });

        $this->app->singleton(OrientadorService::class, function ($app) {
            return new OrientadorService($app->make(OrientadorRepositoryInterface::class));
        });

        $this->app->singleton(SuperAdminService::class, function ($app) {
            return new SuperAdminService(
                $app->make(SuperAdminRepositoryInterface::class),
                $app->make(ImageService::class)
            );
        });

        $this->app->singleton(RutaService::class, function ($app) {
            return new RutaService($app->make(RutaRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('longBinary', function ($column) {
            return $this->addColumn('longBinary', $column);
        });

        MySqlGrammar::macro('typeLongBinary', function (Fluent $column) {
            return 'longblob'; // Tipo equivalente en MySQL
        });
    }
}

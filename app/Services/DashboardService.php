<?php

namespace App\Services;


use App\Repositories\Dashboard\DashboardRepositoryInterface;


class DashboardService
{

    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository){
        $this->dashboardRepository = $dashboardRepository;
    }

    // Calcula el promedio de asesorías mensuales y anuales para un año específico
    public function getAverageAsesorias($year)
    {
        return $this->dashboardRepository->getAverageAsesorias($year);
    }

    public function getTopAliados()
    {
        return $this->dashboardRepository->getTopAliados();
    }
    

    public function getAsesoriasAsignadasSinAsignar()
    {
        return $this->dashboardRepository->getAsesoriasAsignadasSinAsignar();
    }

    public function getConteoRegistrosAnioYMes()
    {
        return $this->dashboardRepository->getConteoRegistrosAnioYMes();
    }

    public function getEmprendedoresPorDepartamento()
    {
        return $this->dashboardRepository->getEmprendedoresPorDepartamento();
    }

    public function getGeneros()
    {
        return $this->dashboardRepository->getGeneros();
    }

    public function getDashboardAliado($idAliado)
    {
        return $this->dashboardRepository->getDashboardAliado($idAliado);
    }

    public function getRadarChartData($id_empresa, $tipo)
    {
        return $this->dashboardRepository->getRadarChartData($id_empresa, $tipo);
    }

    public function getAsesoriasTotalesAliado($anio)
    {
        return $this->dashboardRepository->getAsesoriasTotalesAliado($anio);
    }

    public function getAsesoriasPorMes($id)
    {
        return $this->dashboardRepository->getAsesoriasPorMes($id);
    }

    public function getUsersByRoleAndState()
    {
        return $this->dashboardRepository->getUsersByRoleAndState();
    }
}

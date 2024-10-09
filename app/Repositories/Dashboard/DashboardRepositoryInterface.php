<?php

namespace App\Repositories\Dashboard;

interface DashboardRepositoryInterface
{
    public function getAverageAsesorias($year);
    public function getTopAliados();
    public function getAsesoriasAsignadasSinAsignar();
    public function getConteoRegistrosAnioYMes();
    public function getEmprendedoresPorDepartamento();
    public function getGeneros();
    public function getDashboardAliado($idAliado);
    public function getRadarChartData($id_empresa, $tipo);
    public function getAsesoriasTotalesAliado($anio);
    public function getAsesoriasPorMes($id);
    public function getUsersByRoleAndState();
    
}

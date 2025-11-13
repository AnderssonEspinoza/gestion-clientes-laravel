@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Header con filtros --}}
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="header-title">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        <h1 class="mb-0">Dashboard de Reportes</h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <form method="GET" action="{{ route('reporte.index') }}" class="filter-form">
                        <div class="filter-group">
                            <select name="periodo" class="form-select">
                                <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este Mes</option>
                                <option value="trimestre" {{ request('periodo') == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                                <option value="año" {{ request('periodo') == 'año' ? 'selected' : '' }}>Año</option>
                            </select>
                            <select name="sucursal" class="form-select">
                                <option value="">Todas las sucursales</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" {{ request('sucursal') == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Aplicar
                            </button>
                            <a href="{{ route('reporte.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        {{-- KPIs Mejorados --}}
        <div class="kpis-section">
            <div class="file-kpis row g-3 g-lg-4" >
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-primary">
                        <div class="kpi-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-label">Total Clientes</div>
                            <div class="kpi-value">{{ number_format($kpis['totalClientes']) }}</div>
                            <div class="kpi-change {{ $kpis['totalClientesDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalClientesDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalClientesDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                        <div class="kpi-bg-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-success">
                        <div class="kpi-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-label">Ventas Concretadas</div>
                            <div class="kpi-value">{{ number_format($kpis['ventasConcretadas']) }}</div>
                            <div class="kpi-change {{ $kpis['ventasConcretadasDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['ventasConcretadasDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['ventasConcretadasDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                        <div class="kpi-bg-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-info">
                        <div class="kpi-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-label">Total Asesores</div>
                            <div class="kpi-value">{{ number_format($kpis['totalAsesores']) }}</div>
                            <div class="kpi-change {{ $kpis['totalAsesoresDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalAsesoresDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalAsesoresDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                        <div class="kpi-bg-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-warning">
                        <div class="kpi-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-label">Total Sucursales</div>
                            <div class="kpi-value">{{ number_format($kpis['totalSucursales']) }}</div>
                            <div class="kpi-change {{ $kpis['totalSucursalesDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalSucursalesDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalSucursalesDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                        <div class="kpi-bg-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid de Gráficas Responsive --}}
        <div class="charts-section">
            {{-- Primera fila: Historial principal + Clientes por Estado --}}
            <div class="first-file row g-3 g-lg-4 mb-3 mb-lg-4" >
                <div class="grafica-ha col-lg-8 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="fas fa-chart-area text-primary me-2"></i>
                                Historial de Atenciones
                            </div>
                            <div class="chart-actions">
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="historialChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grafica-ce col-lg-4 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="fas fa-chart-pie text-success me-2"></i>
                                Clientes por Estado
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="clientesEstadoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Segunda fila: Ventas por Sucursal + Clientes por Asesor --}}
            <div class="second-file row g-3 g-lg-4">
                <div class="grafica-vs col-lg-6 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="fas fa-chart-bar text-info me-2"></i>
                                Ventas por Sucursal
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="ventasSucursalChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grafica-ca col-lg-6 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="fas fa-users text-warning me-2"></i>
                                Clientes por Asesor
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="clientesAsesorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Asesores --}}
        <div class="table-section">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-table text-primary me-2"></i>
                        Lista de Asesores
                    </div>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Nuevo
                        </button>
                        <button class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-1"></i>Excel
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="table-responsive">
                        <table id="tablaAsesores" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-user me-2"></i>Nombre
                                    </th>
                                    <th>
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </th>
                                    <th>
                                        <i class="fas fa-building me-2"></i>Sucursal
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-cogs me-2"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="user-name">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td>
                                        @if($user->sucursal)
                                            <span class="badge badge-primary">{{ $user->sucursal->nombre }}</span>
                                        @else
                                            <span class="badge badge-secondary">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Variables CSS */
:root {
    --primary-color: #4e73df;
    --success-color: #1cc88a;
    --info-color: #36b9cc;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --dark-color: #2d3436;
    --light-color: #f8f9fc;
    --white: #ffffff;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-600: #6c757d;
    --gray-800: #343a40;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --border-radius: 0.5rem;
    --border-radius-lg: 0.75rem;
}

/* Layout Principal */
.dashboard-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Responsive */
.dashboard-header {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem 1rem;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 0;
    z-index: 1020;
}

.header-title {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.header-title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin: 0;
}

.filter-form {
    width: 100%;
}

.filter-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
}

.filter-group .form-select {
    flex: 1;
    min-width: 140px;
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-300);
    font-size: 0.875rem;
}

.filter-group .btn {
    white-space: nowrap;
    font-size: 0.875rem;
}

/* Contenido Principal */
.dashboard-content {
    padding: 1rem;
}

/* KPIs Responsive */
.kpis-section {
    margin-bottom: 1.5rem;
}
.file-kpis{

    display: flex; 
    justify-content: space-around;
}

.kpi-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: 1.25rem;
    box-shadow: var(--shadow);
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    height: auto;
    min-height: 120px;
    display: flex;
    align-items: center;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
}

.kpi-card.kpi-primary {
    border-left: 4px solid var(--primary-color);
}

.kpi-card.kpi-success {
    border-left: 4px solid var(--success-color);
}

.kpi-card.kpi-info {
    border-left: 4px solid var(--info-color);
}

.kpi-card.kpi-warning {
    border-left: 4px solid var(--warning-color);
}

.kpi-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--white);
    margin-right: 1rem;
    flex-shrink: 0;
}

.kpi-primary .kpi-icon {
    background: linear-gradient(135deg, var(--primary-color), #6c5ce7);
}

.kpi-success .kpi-icon {
    background: linear-gradient(135deg, var(--success-color), #00b894);
}

.kpi-info .kpi-icon {
    background: linear-gradient(135deg, var(--info-color), #0984e3);
}

.kpi-warning .kpi-icon {
    background: linear-gradient(135deg, var(--warning-color), #fdcb6e);
}

.kpi-content {
    flex: 1;
    z-index: 2;
}

.kpi-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.kpi-change {
    font-size: 0.7rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kpi-change.positive {
    color: var(--success-color);
}

.kpi-change.negative {
    color: var(--danger-color);
}

.kpi-bg-icon {
    position: absolute;
    right: -5px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 3rem;
    color: rgba(0, 0, 0, 0.05);
    z-index: 1;
}

/* Sección de Gráficas */
.charts-section {
    margin-bottom: 1.5rem;
}
.first-file, .second-file{
    display: flex;
    justify-content: space-between;
}


.grafica-ha, .grafica-ce, .grafica-vs, .grafica-ca{
    width: 49%;
}

.chart-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    border: none;
    transition: all 0.3s ease;
    height: 100%;
}

.chart-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
}

.chart-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gray-100);
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chart-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--dark-color);
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.chart-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.chart-body {
    padding: 1.25rem;
    position: relative;
}

.chart-body canvas {
    max-height: 280px;
    width: 100% !important;
    height: auto !important;
}

/* Tabla Responsive */
.table-section {
    margin-bottom: 1.5rem;
}

.table {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.table th {
    background: var(--gray-100);
    border-bottom: 2px solid var(--gray-200);
    font-weight: 600;
    color: var(--dark-color);
    padding: 0.75rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-200);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), #6c5ce7);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.user-name {
    font-weight: 500;
    color: var(--dark-color);
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius);
    font-size: 0.7rem;
    font-weight: 500;
}

.badge-primary {
    background: rgba(78, 115, 223, 0.1);
    color: var(--primary-color);
    border: 1px solid rgba(78, 115, 223, 0.2);
}

.badge-secondary {
    background: rgba(108, 117, 125, 0.1);
    color: var(--gray-600);
    border: 1px solid rgba(108, 117, 125, 0.2);
}

#tablaAsesores{
    width: 600px;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .kpi-value {
        font-size: 1.5rem;
    }
    
    .chart-body canvas {
        max-height: 250px;
    }
    
}

@media (max-width: 992px) {
    .header-title {
        margin-bottom: 1rem;
    }
    
    .header-title h1 {
        font-size: 1.25rem;
    }
    
    .filter-group {
        justify-content: flex-start;
    }
    .file-kpis{
        flex-direction: column;
        gap: 20px;
    }
    .kpi-card {
        min-height: 100px;
        padding: 1rem;
    }
    
    .kpi-value {
        font-size: 1.25rem;
    }
    
    .kpi-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    .first-file, .second-file{
        flex-direction: column;
        gap: 20px;
    }
    .grafica-ha, .grafica-ce, .grafica-vs, .grafica-ca{
        width: 100%;
    }
    
    .chart-body canvas {
        max-height: 220px;
    }
}

@media (max-width: 768px) {
    .dashboard-content {
        padding: 0.75rem;
    }
    
    .dashboard-header {
        padding: 0.75rem 0;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .filter-group .form-select,
    .filter-group .btn {
        width: 100%;
        min-width: auto;
    }
    
    .chart-header {
        padding: 0.75rem 1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .chart-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    .chart-body {
        padding: 1rem;
    }
    
    .chart-body canvas {
        max-height: 200px;
    }
    
    .kpi-card {
        min-height: 90px;
        padding: 0.75rem;
    }
    
    .kpi-value {
        font-size: 1.1rem;
    }
    
    .kpi-icon {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
        margin-right: 0.75rem;
    }
    
    .kpi-bg-icon {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .user-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .chart-title {
        font-size: 0.85rem;
    }
    
    .kpi-label {
        font-size: 0.65rem;
    }
    
    .kpi-change {
        font-size: 0.65rem;
    }
}

/* DataTable Customization */
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter input {
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-300);
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_length {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 1rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.dataTables_wrapper .dataTables_paginate {
    padding-top: 1rem;
}

/* Mejoras adicionales para responsive */
@media (max-width: 480px) {
    .kpis-section .row {
        --bs-gutter-x: 0.75rem;
    }
    
    .charts-section .row {
        --bs-gutter-x: 0.75rem;
    }
    
    .dashboard-content {
        padding: 0.5rem;
    }
}
</style>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Configurar DataTable
    $('#tablaAsesores').DataTable({
        responsive: true,
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros por página",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            emptyTable: "No hay datos disponibles en la tabla"
        },
        pageLength: 10
    });

    // Datos desde backend
    const nombresSucursales = {!! json_encode($datos->pluck('nombre')->toArray()) !!};
    const ventas = {!! json_encode($datos->pluck('ventas')->toArray()) !!};
    const estados = {!! json_encode(array_keys($clientesPorEstado)) !!};
    const clientesEstado = {!! json_encode(array_values($clientesPorEstado)) !!};
    const fechasHistorial = {!! json_encode($data->pluck('fecha')->toArray()) !!};
    const totalAtenciones = {!! json_encode($data->pluck('total')->toArray()) !!};
    const nombresAsesores = {!! json_encode($clientesPorAsesor->pluck('name')->toArray()) !!};
    const totalClientes = {!! json_encode($clientesPorAsesor->pluck('total')->toArray()) !!};

    // Configuración global para charts
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#6c757d';

    // Paleta de colores
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        purple: '#6f42c1',
        pink: '#e83e8c',
        orange: '#fd7e14'
    };

    // Configuración responsive para charts
    const responsiveOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 10
                    }
                }
            }
        }
    };

    // Historial de Atenciones
    new Chart(document.getElementById('historialChart'), {
        type: 'line',
        data: {
            labels: fechasHistorial,
            datasets: [{
                label: 'Atenciones',
                data: totalAtenciones,
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            ...responsiveOptions,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });

    // Clientes por Estado
    new Chart(document.getElementById('clientesEstadoChart'), {
        type: 'doughnut',
        data: {
            labels: ['Venta concretada', 'No califica', 'Inactivo', 'Pendiente', 'En evaluacion', 'Califica'],
            datasets: [{
                data: clientesEstado,
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.danger,
                    colors.purple
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            ...responsiveOptions,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 10,
                        font: {
                            size: 9
                        }
                    }
                }
            }
        }
    });

    // Ventas por Sucursal
    new Chart(document.getElementById('ventasSucursalChart'), {
        type: 'bar',
        data: {
            labels: nombresSucursales,
            datasets: [{
                label: 'Ventas',
                data: ventas,
                backgroundColor:[
                    colors.purple,
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.danger
                    
                ],
                borderColor: [
                    colors.purple,
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.danger
                    
                ],
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            ...responsiveOptions,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });

    // Clientes por Asesor
    new Chart(document.getElementById('clientesAsesorChart'), {
        type: 'bar',
        data: {
            labels: nombresAsesores,
            datasets: [{
                label: 'Clientes',
                data: totalClientes,
                backgroundColor:[
                    colors.orange,
                    colors.pink,
                    colors.purple,
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.danger
                    
                ],
                borderColor: colors.info,
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            ...responsiveOptions,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
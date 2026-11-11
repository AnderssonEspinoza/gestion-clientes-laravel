@extends('layouts.app')

@section('content')
<div class="dashboard-container cm-page">
    {{-- Header con filtros --}}
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="header-title">

                        <h1 class="mb-0">Reportes</h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <form method="GET" action="{{ route('reporte.index') }}" class="filter-form">
                        <div class="filter-group">
                            <select name="periodo" class="form-select" aria-label="Período">
                                <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este Mes</option>
                                <option value="trimestre" {{ request('periodo') == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                                <option value="año" {{ request('periodo') == 'año' ? 'selected' : '' }}>Año</option>
                            </select>
                            <select name="sucursal" class="form-select" aria-label="Sucursal">
                                <option value="">Todas las sucursales</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" {{ request('sucursal') == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary">
                                Aplicar
                            </button>
                            <a href="{{ route('reporte.index') }}" class="btn btn-outline-secondary">
                                Limpiar
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
                        <div class="kpi-content">
                            <div class="kpi-label">Total Clientes</div>
                            <div class="kpi-value">{{ number_format($kpis['totalClientes']) }}</div>
                            <div class="kpi-change {{ $kpis['totalClientesDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalClientesDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalClientesDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-success">
                        <div class="kpi-content">
                            <div class="kpi-label">Ventas Concretadas</div>
                            <div class="kpi-value">{{ number_format($kpis['ventasConcretadas']) }}</div>
                            <div class="kpi-change {{ $kpis['ventasConcretadasDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['ventasConcretadasDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['ventasConcretadasDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-info">
                        <div class="kpi-content">
                            <div class="kpi-label">Total Asesores</div>
                            <div class="kpi-value">{{ number_format($kpis['totalAsesores']) }}</div>
                            <div class="kpi-change {{ $kpis['totalAsesoresDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalAsesoresDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalAsesoresDelta']), 1) }}% vs período anterior
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="kpi-card kpi-warning">
                        <div class="kpi-content">
                            <div class="kpi-label">Total Sucursales</div>
                            <div class="kpi-value">{{ number_format($kpis['totalSucursales']) }}</div>
                            <div class="kpi-change {{ $kpis['totalSucursalesDelta'] >= 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $kpis['totalSucursalesDelta'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($kpis['totalSucursalesDelta']), 1) }}% vs período anterior
                            </div>
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

                                Historial de Atenciones
                            </div>
                            @unless (config('demo.enabled'))
                                <div class="chart-actions">
                                    <button class="btn btn-sm btn-outline-secondary" aria-label="Descargar historial">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 3v12m-4-4 4 4 4-4M5 16v4h14v-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>

                            @endunless
                        </div>
                        <div class="chart-body">
                            <canvas id="historialChart" role="img" aria-label="Historial de atenciones"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grafica-ce col-lg-4 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">

                                Clientes por Estado
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="clientesEstadoChart" role="img" aria-label="Clientes por estado"></canvas>
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

                                Ventas por Sucursal
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="ventasSucursalChart" role="img" aria-label="Ventas por sucursal"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grafica-ca col-lg-6 col-md-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">

                                Clientes por Asesor
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="clientesAsesorChart" role="img" aria-label="Clientes por asesor"></canvas>
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

                        Lista de Asesores
                    </div>
                    @unless (config('demo.enabled'))
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-primary">
                                Nuevo
                            </button>
                            <button class="btn btn-sm btn-success">
                                Excel
                            </button>
                        </div>

                    @endunless
                </div>
                <div class="chart-body">
                    <p class="cm-table-hint">Desliza la tabla para ver todas las columnas.</p>
                    <div class="table-responsive">
                        <table id="tablaAsesores" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Nombre
                                    </th>
                                    <th>
                                        Email
                                    </th>
                                    <th>
                                        Sucursal
                                    </th>
                                    <th class="text-center">
                                        Acciones
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
                                        @unless (config('demo.enabled'))
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" aria-label="Editar asesor">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m5 15-1 5 5-1L20 8l-4-4z" stroke-linejoin="round"/></svg>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info" aria-label="Ver asesor">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                                </button>
                                            </div>

                                        @endunless
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


{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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

    document.querySelectorAll('.kpi-change').forEach(cambio => {
        if (cambio.textContent.trim().startsWith('0.0%')) cambio.classList.add('kpi-change-neutral');
    });

    const tablaDesplazable = document.querySelector('.dataTables_wrapper .row:has(table)');
    if (tablaDesplazable) {
        tablaDesplazable.tabIndex = 0;
        tablaDesplazable.setAttribute('role', 'region');
        tablaDesplazable.setAttribute('aria-label', 'Tabla de asesores');
    }

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
    Chart.defaults.font.family = "'Instrument Sans', sans-serif";
    Chart.defaults.font.size = 11;
    const theme = getComputedStyle(document.documentElement);
    Chart.defaults.color = theme.getPropertyValue('--cm-muted').trim();
    const gridColor = theme.getPropertyValue('--cm-line').trim();
    Chart.defaults.animation = false;

    // Paleta de colores
    const colors = {
        primary: theme.getPropertyValue('--cm-accent').trim(),
        success: '#4275a7',
        info: '#5a8690',
        warning: '#ae751a',
        danger: '#b64d60',
        purple: '#7762a7',
        pink: '#a2527d',
        orange: '#a95c37'
    };

    const etiquetasEstado = {
        venta_concretada: 'Venta concretada',
        no_califica: 'No califica',
        inactivo: 'Inactivo',
        pendiente: 'Pendiente',
        en_evaluacion: 'En evaluación',
        califica: 'Califica',
    };
    const coloresEstado = {
        venta_concretada: colors.primary,
        no_califica: colors.danger,
        inactivo: colors.info,
        pendiente: colors.warning,
        en_evaluacion: colors.success,
        califica: colors.purple,
    };

    function describirGrafico(id, etiquetas, valores) {
        const canvas = document.getElementById(id);
        const titulo = canvas.getAttribute('aria-label');
        if (valores.length === 0) {
            canvas.parentElement.dataset.chartEmpty = 'Sin datos para mostrar';
            canvas.setAttribute('aria-label', titulo + ': sin datos para mostrar');
        } else {
            const valoresVisibles = etiquetas.map((etiqueta, i) => etiqueta + ': ' + valores[i]);
            canvas.setAttribute('aria-label', titulo + '. ' + valoresVisibles.join(', '));
        }
    }

    const etiquetasClientes = estados.map(estado => etiquetasEstado[estado] ?? estado);
    describirGrafico('historialChart', fechasHistorial, totalAtenciones);
    describirGrafico('clientesEstadoChart', etiquetasClientes, clientesEstado);
    describirGrafico('ventasSucursalChart', nombresSucursales, ventas);
    describirGrafico('clientesAsesorChart', nombresAsesores, totalClientes);

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
                borderWidth: 2,
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
                        color: gridColor
                    },
                    ticks: {
                        precision: 0,
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
            labels: etiquetasClientes,
            datasets: [{
                data: clientesEstado,
                backgroundColor: estados.map(estado => coloresEstado[estado] ?? colors.info),
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            ...responsiveOptions,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 10,
                        font: {
                            size: 11
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
                maxBarThickness: 48,
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
                        color: gridColor
                    },
                    ticks: {
                        precision: 0,
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
                maxBarThickness: 48,
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
                        color: gridColor
                    },
                    ticks: {
                        precision: 0,
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
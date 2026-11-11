@extends('layouts.app')

@section('content')
    <div class="cm-page cm-report-detail">
        <h1 class="cm-title cm-title-spaced">Detalles de KPI: {{ $tipo }}</h1>

        {{-- Agrega aquí la lógica para mostrar los detalles del KPI según el tipo --}}
        <div class="cm-detail-card">
            <div class="cm-detail-content">
                <h5 class="card-title">Detalles de {{ $tipo }}</h5>
                {{-- Mostrar más información según el KPI seleccionado --}}
            </div>
        </div>
    </div>
@endsection

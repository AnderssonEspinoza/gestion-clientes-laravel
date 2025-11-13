@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Detalles de KPI: {{ $tipo }}</h1>

        {{-- Agrega aquí la lógica para mostrar los detalles del KPI según el tipo --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detalles de {{ $tipo }}</h5>
                {{-- Mostrar más información según el KPI seleccionado --}}
            </div>
        </div>
    </div>
@endsection

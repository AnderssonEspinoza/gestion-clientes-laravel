@props(['status'])

@if ($status)
    <div role="status" data-cm-status {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif

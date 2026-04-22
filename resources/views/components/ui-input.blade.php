@props([
    'label' => null,
    'error' => null,
    'name' => null,
])

@php
    $fieldError = $error;

    if (!$fieldError && $name) {
        $fieldError = $errors->first($name);
    }
@endphp

<div class="space-y-1">
    @if ($label)
        <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <input
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm
                        focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900',
        ]) }} />

    @if ($fieldError)
        <p class="text-sm text-red-600">{{ $fieldError }}</p>
    @endif
</div>

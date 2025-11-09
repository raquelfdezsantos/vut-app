@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[color:var(--color-text-secondary)]']) }}>
    {{ $value ?? $slot }}
</label>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] text-white font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150 focus:outline-none focus:ring-1 focus:ring-[color:var(--color-accent)] focus:ring-offset-1']) }} style="border-radius: 2px; white-space: nowrap;">
    {{ $slot }}
</button>

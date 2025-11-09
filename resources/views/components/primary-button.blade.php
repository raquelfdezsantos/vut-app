<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2 rounded bg-[color:var(--color-accent)] text-white font-medium hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-[color:var(--color-accent)] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>

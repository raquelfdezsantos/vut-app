<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <strong>Revisa los siguientes campos:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus autocomplete="username" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            autocomplete="current-password" />
        </div>

        <!-- Recuérdame -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" name="remember" style="accent-color: var(--color-accent);">
                <span class="ms-2 text-sm text-[color:var(--color-text-secondary)]">Recuérdame</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm transition-colors" style="color: var(--color-text-secondary); text-decoration: none;" href="{{ route('password.request') }}"
                   onmouseover="this.style.color='var(--color-accent)';"
                   onmouseout="this.style.color='var(--color-text-secondary)';">
                    ¿Has olvidado tu contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Iniciar sesión
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

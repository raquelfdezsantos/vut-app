<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
                   onmouseover="this.style.color='var(--color-accent)'; this.style.textDecoration='underline';"
                   onmouseout="this.style.color='var(--color-text-secondary)'; this.style.textDecoration='none';">
                    ¿Has olvidado tu contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Iniciar sesión
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

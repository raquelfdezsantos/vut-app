<x-guest-layout>
    <div class="mb-4 text-sm" style="color: var(--color-text-secondary);">
        ¡Gracias por registrarte! Antes de empezar, por favor verifica tu correo electrónico haciendo clic en el enlace que te acabamos de enviar. Si no lo recibiste, podemos enviarte otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm" style="color:#16a34a;">
            Se ha enviado un nuevo enlace de verificación al correo que proporcionaste durante el registro.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Reenviar correo de verificación
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--color-accent)]" style="color: var(--color-text-secondary);">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>

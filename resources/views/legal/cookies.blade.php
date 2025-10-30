<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Política de Cookies
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 prose prose-sm max-w-none">
                    <h2>1. ¿QUÉ SON LAS COOKIES?</h2>
                    <p>
                        Las cookies son pequeños archivos de texto que se almacenan en su dispositivo cuando visita un sitio web. Las cookies permiten que el sitio web recuerde sus acciones y preferencias durante un período de tiempo.
                    </p>

                    <h2>2. COOKIES UTILIZADAS EN ESTE SITIO WEB</h2>
                    <p>
                        Este sitio web utiliza únicamente <strong>cookies técnicas estrictamente necesarias</strong> para su funcionamiento:
                    </p>

                    <h3>Cookies de sesión (Laravel)</h3>
                    <table class="min-w-full border border-gray-300 mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2 text-left">Cookie</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Finalidad</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><code>{{ config('session.cookie') }}</code></td>
                                <td class="border border-gray-300 px-4 py-2">Identificador de sesión del usuario. Necesaria para mantener su sesión iniciada.</td>
                                <td class="border border-gray-300 px-4 py-2">Sesión (se elimina al cerrar el navegador)</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><code>XSRF-TOKEN</code></td>
                                <td class="border border-gray-300 px-4 py-2">Protección contra ataques CSRF (Cross-Site Request Forgery). Seguridad del sitio.</td>
                                <td class="border border-gray-300 px-4 py-2">2 horas</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="mt-6">Cookie de consentimiento</h3>
                    <table class="min-w-full border border-gray-300 mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2 text-left">Cookie</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Finalidad</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><code>cookie_consent</code></td>
                                <td class="border border-gray-300 px-4 py-2">Almacena su preferencia sobre el uso de cookies para no mostrar el banner repetidamente.</td>
                                <td class="border border-gray-300 px-4 py-2">1 año</td>
                            </tr>
                        </tbody>
                    </table>

                    <h2 class="mt-6">3. FINALIDAD DE LAS COOKIES</h2>
                    <p>
                        Las cookies técnicas son necesarias para:
                    </p>
                    <ul>
                        <li>Mantener su sesión de usuario activa mientras navega por el sitio.</li>
                        <li>Proteger el sitio web contra ataques de seguridad.</li>
                        <li>Recordar su consentimiento sobre el uso de cookies.</li>
                    </ul>

                    <h2>4. COOKIES DE TERCEROS</h2>
                    <p>
                        <strong>Este sitio web NO utiliza cookies de terceros</strong> para publicidad, analítica u otros fines. No se comparten datos con terceros a través de cookies.
                    </p>

                    <h2>5. CÓMO GESTIONAR LAS COOKIES</h2>
                    <p>
                        Puede configurar su navegador para rechazar cookies, pero esto puede afectar al funcionamiento correcto del sitio web. Al ser cookies técnicas necesarias, su eliminación impedirá el uso normal de las funcionalidades del sitio.
                    </p>
                    <p>
                        Para gestionar las cookies en los navegadores más comunes:
                    </p>
                    <ul>
                        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" class="text-indigo-600">Google Chrome</a></li>
                        <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" class="text-indigo-600">Mozilla Firefox</a></li>
                        <li><a href="https://support.apple.com/es-es/HT201265" target="_blank" class="text-indigo-600">Safari</a></li>
                        <li><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" class="text-indigo-600">Microsoft Edge</a></li>
                    </ul>

                    <h2>6. CONSENTIMIENTO</h2>
                    <p>
                        Al navegar y utilizar este sitio web, acepta el uso de las cookies técnicas necesarias descritas en esta política.
                    </p>

                    <h2>7. MÁS INFORMACIÓN</h2>
                    <p>
                        Para más información sobre el tratamiento de datos personales, consulte nuestra <a href="{{ route('legal.privacidad') }}" class="text-indigo-600 hover:text-indigo-800">Política de Privacidad</a>.
                    </p>

                    <p class="text-sm text-gray-500 mt-8">
                        Última actualización: {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

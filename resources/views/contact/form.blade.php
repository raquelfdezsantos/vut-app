<x-guest-layout>
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-xl font-semibold mb-4">Contacto</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}">
            @csrf

            <label class="block mb-2 text-sm">Nombre</label>
            <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2 mb-2" required>
            @error('name') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <label class="block mb-2 text-sm">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2 mb-2" required>
            @error('email') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <label class="block mb-2 text-sm">Mensaje</label>
            <textarea name="message" rows="6" class="w-full border rounded p-2 mb-3"
                required>{{ old('message') }}</textarea>
            @error('message') <p class="text-red-600 text-sm mb-3">{{ $message }}</p> @enderror

            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Enviar
            </button>
        </form>
    </div>
</x-guest-layout>
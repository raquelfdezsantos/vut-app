{{-- resources/views/public/reservar.blade.php --}}
@extends('layouts.app')
@section('title','Reservar – Staynest')

@section('content')
<section class="mx-auto max-w-3xl px-4 lg:px-6" x-data="booking()">
  <h1 class="font-dmserif text-3xl mb-6">Reservar</h1>

  <form @submit.prevent="go()">
    {{-- propiedad --}}
    <label class="block text-sm mb-1">Alojamiento</label>
    <select x-model="property_id" class="w-full mb-4 bg-[#111] border border-sn-accent/40 rounded-xl px-4 py-2.5">
      @foreach(\App\Models\Property::orderBy('name')->get() as $p)
        <option value="{{ $p->id }}" @selected($loop->first)>{{ $p->name }}</option>
      @endforeach
    </select>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1">Entrada</label>
        <input type="date" x-model="check_in" @change="quote()" class="w-full bg-[#111] border border-sn-accent/40 rounded-xl px-4 py-2.5"/>
      </div>
      <div>
        <label class="block text-sm mb-1">Salida</label>
        <input type="date" x-model="check_out" @change="quote()" class="w-full bg-[#111] border border-sn-accent/40 rounded-xl px-4 py-2.5"/>
      </div>
      <div>
        <label class="block text-sm mb-1">Huéspedes</label>
        <input type="number" min="1" x-model.number="guests" @input.debounce.300ms="quote()" class="w-full bg-[#111] border border-sn-accent/40 rounded-xl px-4 py-2.5"/>
      </div>
    </div>

    <div class="mt-4 p-4 rounded-2xl border border-sn-accent/40 bg-[#0A0A0A]">
      <template x-if="loading"><p class="text-sn-mute text-sm">Calculando…</p></template>
      <template x-if="!loading">
        <div>
          <p class="text-sm text-sn-mute">Noches: <span x-text="nights ?? '-'"></span></p>
          <p class="text-xl mt-1">Total: <span x-text="total ? (total.toFixed(2)+' €') : '-'"></span></p>
        </div>
      </template>
    </div>

    <div class="mt-6 flex gap-3">
      <button type="submit" class="px-5 py-3 rounded-full bg-sn-accent hover:opacity-90" :disabled="!canBook()">Reservar</button>
      <a href="{{ route('home') }}" class="px-5 py-3 rounded-full border border-sn-accent hover:bg-sn-accent/10">Volver</a>
    </div>

    {{-- Toast login requerido --}}
    <div x-show="warn" x-transition
         class="mt-4 text-sm px-3 py-2 rounded bg-yellow-600/30 border border-yellow-500/40">
      Para reservar debes iniciar sesión o registrarte. Redirigiendo…
    </div>
  </form>
</section>

@push('scripts')
<script>
  window.booking = () => ({
    property_id: document.querySelector('select')?.value ?? null,
    check_in: '', check_out: '', guests: 1,
    nights: null, total: null, loading: false, warn: false,
    canBook(){ return this.property_id && this.check_in && this.check_out && this.guests>0 && this.total },
    async quote(){
      if(!this.property_id || !this.check_in || !this.check_out || !this.guests) return;
      this.loading = true;
      try{
        const q = new URLSearchParams({
          property_id: this.property_id,
          check_in: this.check_in,
          check_out: this.check_out,
          guests: this.guests
        });
        const res = await fetch(`/api/quote?${q}`);
        const data = await res.json();
        if(!data.ok) throw new Error(data.message || 'No disponible');
        this.nights = data.nights; this.total = data.total;
      }catch(e){
        this.nights = null; this.total = null; console.error(e);
      }finally{
        this.loading = false;
      }
    },
    async go(){
      // si no hay sesión, aviso y a login con intended de vuelta aquí
      @guest
        this.warn = true;
        setTimeout(() => {
          const params = new URLSearchParams({
            returnTo: window.location.pathname 
          }).toString();
          window.location.href = `{{ route('login') }}?${params}`;
        }, 800);
        return;
      @endguest

      // Si hay sesión, POST a ReservationController@store 
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `{{ route('reservas.store') }}`;
      form.innerHTML = `
        @csrf
        <input type="hidden" name="property_id" value="${this.property_id}">
        <input type="hidden" name="check_in" value="${this.check_in}">
        <input type="hidden" name="check_out" value="${this.check_out}">
        <input type="hidden" name="guests" value="${this.guests}">
      `;
      document.body.appendChild(form); form.submit();
    }
  })
</script>
@endpush
@endsection

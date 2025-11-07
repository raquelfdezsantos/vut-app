<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuoteController extends Controller
{
    public function show(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required','integer','exists:properties,id'],
            'check_in'    => ['required','date'],
            'check_out'   => ['required','date','after:check_in'],
            'guests'      => ['required','integer','min:1'],
        ]);

        $property = Property::findOrFail($data['property_id']);

        // Construye rango de noches
        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates = collect($period)->map->toDateString();

        if ($dates->isEmpty()) {
            throw ValidationException::withMessages(['check_out' => 'El rango de fechas no es válido.']);
        }

        // Reutiliza lógica: sumar precios por noche * guests
        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $dates)
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        // Verifica que haya tarifa para todas las noches (opcional)
        if ($rates->count() !== $dates->count()) {
            return response()->json([
                'ok' => false,
                'message' => 'Fechas no disponibles.',
            ], 422);
        }

        $nights = $rates->count();
        $total = $rates->sum('price') * (int)$data['guests'];

        return response()->json([
            'ok' => true,
            'nights' => $nights,
            'total' => $total,
        ]);
    }
}

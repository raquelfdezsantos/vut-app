<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with('photos')->latest()->paginate(6);
        return view('properties.index', compact('properties'));
    }

    public function show(\App\Models\Property $property)
{
    $property->load([
        'photos',
        'rateCalendar' => function ($q) {
            $q->where('is_available', true)
              ->whereDate('date', '>=', now()->toDateString())
              ->orderBy('date');
        },
    ]);

    $fromPrice = optional($property->rateCalendar->first())->price ?? null;

    return view('property.show', compact('property', 'fromPrice'));
}
}


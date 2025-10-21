<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RateCalendar>
 */
class RateCalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30))->toDateString(),
            'price' => $this->faker->randomFloat(2, 50, 200),
            'is_available' => true,
            'min_stay' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

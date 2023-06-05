<?php

namespace Database\Factories;

use App\Models\FundManager;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => "{$this->faker->currencyCode()}-{$this->faker->countryISOAlpha3()}",
            'year' => $this->faker->numberBetween(1980, 2023),
            'fund_manager_id' => FundManager::query()->get()->random()->id,
        ];
    }
}

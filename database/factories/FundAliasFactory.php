<?php

namespace Database\Factories;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundAliasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->colorName(),
            'fund_id' => Fund::query()->get()->random()->id,
        ];
    }
}

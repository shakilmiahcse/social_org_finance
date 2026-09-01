<?php

namespace Database\Factories;

use App\Models\Fund;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundFactory extends Factory
{
    protected $model = Fund::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Fund',
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['main', 'campaign']),
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
        ];
    }
}

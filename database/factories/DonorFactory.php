<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonorFactory extends Factory
{
    protected $model = Donor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'blood_group' => fake()->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-']),
            'address' => fake()->address(),
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
        ];
    }
}

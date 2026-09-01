<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Fund;
use App\Models\Donor;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'txn_id' => 'TXN' . fake()->unique()->numerify('##########'),
            'amount' => fake()->randomFloat(2, 500, 50000),
            'type' => fake()->randomElement(['credit', 'debit']),
            'purpose' => fake()->sentence(),
            'payment_method' => fake()->randomElement(['cash', 'bkash', 'bank', 'nagad', 'rocket', 'card']),
            'status' => 'completed',
            'organization_id' => Organization::factory(),
            'fund_id' => Fund::factory(),
            'donor_id' => Donor::factory(),
            'created_by' => User::factory(),
        ];
    }
}

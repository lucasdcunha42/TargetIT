<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'district' => $this->faker->cityPrefix(), // Alterado de neighborhood para district
            'complement' => $this->faker->secondaryAddress(),
            'zip_code' => str_pad($this->faker->numberBetween(10000, 99999), 8, '0'), // CEP no formato brasileiro
        ];
    }
}
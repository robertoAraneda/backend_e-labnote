<?php

namespace Database\Factories;

use App\Models\IdentifierPatient;
use App\Models\IdentifierType;
use App\Models\IdentifierUse;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdentifierPatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IdentifierPatient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'value' => 'home',
            'identifier_type_id' => IdentifierType::factory(),
            'identifier_use_id' => IdentifierUse::factory(),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Analyte;
use Illuminate\Database\Seeder;

class AnalyteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Analyte::create([
            'name' => 'GLUCOSA',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'CREATININA',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'HEMOGRAMA SIN CONTEO DIFERENCIAL MANUAL',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'ANTICUERPOS (IGG) ANTI NUCLEARES (ANA)',
            'is_patient_codable' => true,
            'active' => true

        ]);
    }
}

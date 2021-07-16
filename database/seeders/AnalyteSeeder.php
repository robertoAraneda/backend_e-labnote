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
            'name' => 'GLUCOSA SANGRE',
            'slug' => 'glucosa-sangre',
            'clinical_information' => 'La glucosa es el carbohidrato más importante de la sangre periférica que, al oxidarse, constituye la mayor fuente de energía celular en el organismo. La glucosa proveniente de la alimentación se convierte a glucógeno para ser almacenada en el hígado o a ácidos grasos para ser almacenada en el tejido adiposo. El estrecho intervalo de concentración de la glucosa en sangre (glucemia) es controlado por numerosas hormonas, siendo las más importantes las sintetizadas en el páncreas. La causa más frecuente de valores elevados de glucosa (hiperglucemia) es la diabetes mellitus, producida por una deficiencia en la secreción o en la acción de la insulina. Además, existen numerosos factores secundarios que contribuyen a elevar los niveles de glucemia, incluyendo la pancreatitis, la disfunción tiroidea, la insuficiencia renal y las hepatopatías. La disminución de los niveles de glucosa (hipoglucemia) se observa con menor frecuencia. Está causada por estados tales como el insulinoma, el hipopituitarismo o el exceso de insulina.',
            'loinc_id' => '2345-7',
            'availability_id' => 1,
            'process_time_id' => 1,
            'medical_request_type_id' => 1,
            'workarea_id' => 1,
            'created_user_id' => 3,
            'created_user_ip' => '127.0.0.1',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'GLUCOSA ORINA 2° CHORRO',
            'slug' => 'glucosa-orina-2-chorro',
            'clinical_information' => 'En circunstancias normales, la glucosa es fácilmente filtrada por los glomérulos y posteriormente reabsorbida en el túbulo proximal. En condiciones normales la glucosa no se excreta en la orina. Sin embargo, la capacidad del túbulo proximal para reabsorber la glucosa es limitada; si la carga filtrada excede la capacidad de reabsorción del túbulo proximal, una porción de la glucosa filtrada se excretará en la orina. Por lo tanto, las concentraciones elevadas de glucosa en suero (como ocurre en la diabetes mellitus) pueden resultar en un aumento en la carga de filtración de glucosa y pueden abrumar la capacidad de reabsorción de los túbulos dando como resultado glucosuria.',
            'loinc_id' => '2345-7',
            'availability_id' => 1,
            'process_time_id' => 1,
            'medical_request_type_id' => 1,
            'workarea_id' => 1,
            'created_user_id' => 3,
            'created_user_ip' => '127.0.0.1',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'CREATININA SANGRE',
            'slug' => 'creatinina-sangre',
            'clinical_information' => 'La determinación de la creatinina en suero o plasma es la prueba más común para evaluar la función renal. La creatinina, un producto de degradación del fosfato de creatina muscular, suele producirse en el organismo a una tasa relativamente constante según la masa muscular. Se filtra mayormente en los glomérulos y, en condiciones normales, no es reabsorbida por los túbulos en una cantidad apreciable. Una pequeña pero significativa cantidad se secreta activamente.',
            'loinc_id' => '2345-7',
            'availability_id' => 1,
            'process_time_id' => 1,
            'medical_request_type_id' => 1,
            'workarea_id' => 1,
            'created_user_id' => 3,
            'created_user_ip' => '127.0.0.1',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'HEMOGRAMA AUTOMATIZADO',
            'slug' => 'hemograma-automatizado',
            'clinical_information' => 'El hemograma  automatizado es un examen que nos  entrega información sobre el estado de las distintas variables hematológicas , siendo útil para el apoyo diagnóstico y seguimiento de infecciones bacterianas, parasitarias, virales, anemias, leucemias, entre otras.',
            'loinc_id' => '2345-7',
            'availability_id' => 1,
            'process_time_id' => 1,
            'medical_request_type_id' => 1,
            'workarea_id' => 1,
            'created_user_id' => 3,
            'created_user_ip' => '127.0.0.1',
            'is_patient_codable' => true,
            'active' => true

        ]);
        Analyte::create([
            'name' => 'ANTICUERPOS (IGG) ANTI NUCLEARES (ANA) SANGRE',
            'slug' => 'anticuerpos-(igg)-anti-nucleares-(ana)-sangre',
            'clinical_information' => 'El término "anticuerpos antinucleares" (ANA) describe una variedad de autoanticuerpos que reaccionan con lo constituyentes de los núcleos celulares incluyendo el ADN, ARN y varias ribonucleoproteínas. Estos anticuerpos aparecen con elevada frecuencia en pacientes con enfermedades reumáticas o del tejido conectivo. Esta sensibilidad diagnóstica ha conducido a la incorporación del ensayo de ANA en los Criterios Revisados para la Clasificación de Lupus Eritematoso Sistémico de 1982, realizados por el Colegio Americano de Reumatología. La inmunofluorescencia es el método de referencia para los ensayos de ANA.',
            'loinc_id' => '2345-7',
            'availability_id' => 1,
            'process_time_id' => 1,
            'medical_request_type_id' => 1,
            'workarea_id' => 1,
            'created_user_id' => 3,
            'created_user_ip' => '127.0.0.1',
            'is_patient_codable' => true,
            'active' => true

        ]);
    }
}

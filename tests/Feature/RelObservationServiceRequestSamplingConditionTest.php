<?php

namespace Tests\Feature;

use App\Models\Analyte;
use App\Models\ObservationServiceRequest;
use App\Models\Role;
use App\Models\SamplingCondition;
use App\Models\User;
use Database\Seeders\AnalytePermissionSeeder;
use Database\Seeders\ObservationServiceRequestPermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RelObservationServiceRequestSamplingConditionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();


        $this->seed(ObservationServiceRequestPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('observationServiceRequest.create');
        $role->givePermissionTo('observationServiceRequest.index');
        $role->givePermissionTo('observationServiceRequest.show');

        $user->assignRole($role);

        $this->user = $user;
        $this->role = $role;
        $this->model = ObservationServiceRequest::factory()
            ->hasAttached(SamplingCondition::factory()->count(5), ['user_id' => $user->id])
            ->create();
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_de_indicaciones_toma_muestra_de_un_examen(): void
    {

        $url = "/api/v1/observation-service-requests/{$this->model->id}/sampling-conditions";


        $response = $this->actingAs($this->user, 'api')
            ->getJson($url)
            ->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'active' => 'boolean',
            '_links' => 'array',
        ]))
        );
    }

    /**
     * @test
     */
    public function se_puede_obtener_las_indicationes_toma_muestra_asociados_a_un_exameno_del_total_de_indicaciones(): void
    {
        SamplingCondition::factory()->count(5)->create();
        ObservationServiceRequest::factory()
            ->hasAttached(SamplingCondition::factory()->count(10), ['user_id' => $this->user->id])
            ->create();

        $url = "/api/v1/observation-service-requests/{$this->model->id}/sampling-conditions?cross=true";

        $response = $this->actingAs($this->user, 'api')
            ->getJson($url);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'active' => 'boolean',
            '_links' => 'array',
            'checkbox' => 'boolean'
        ]))
        );

    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void
    {

        $analyte = Analyte::factory()->create();
        $samplingCondition = SamplingCondition::factory()->count(6)->create()->pluck('id');

        $stored = $samplingCondition->splice(3);

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/analytes/{$analyte->id}/sampling-conditions",
                $stored->all());

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'active' => 'boolean',
            '_links' => 'array'
        ]))
        );
    }

}

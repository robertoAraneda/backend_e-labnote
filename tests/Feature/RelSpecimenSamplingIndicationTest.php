<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Specimen;
use App\Models\SamplingIndication;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SpecimenPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RelSpecimenSamplingIndicationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();


        $this->seed(SpecimenPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('specimen.create');
        $role->givePermissionTo('specimen.index');
        $role->givePermissionTo('specimen.show');

        $user->assignRole($role);

        $this->user = $user;
        $this->role = $role;
        $this->model = Specimen::factory()
            ->hasAttached(SamplingIndication::factory()->count(5), ['user_id' => $user->id])
            ->create();
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_de_indicaciones_toma_muestra_de_un_examen(): void
    {

        $url = "/api/v1/specimens/{$this->model->id}/sampling-indications";


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
        SamplingIndication::factory()->count(5)->create();
        Specimen::factory()
            ->hasAttached(SamplingIndication::factory()->count(10), ['user_id' => $this->user->id])
            ->create();

        $url = "/api/v1/specimens/{$this->model->id}/sampling-indications?cross=true";

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

        $analyte = Specimen::factory()->create();
        $samplingIndications = SamplingIndication::factory()->count(6)->create()->pluck('id');

        $stored = $samplingIndications->splice(3);

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/specimens/{$analyte->id}/sampling-indications",
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

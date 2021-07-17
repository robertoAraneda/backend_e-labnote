<?php

namespace Tests\Feature;

use App\Http\Controllers\AnalyteController;
use App\Models\Analyte;
use App\Models\Disponibility;
use App\Models\MedicalRequestType;
use App\Models\ProcessTime;
use App\Models\Role;
use App\Models\SamplingCondition;
use App\Models\User;
use App\Models\Workarea;
use Database\Seeders\AnalytePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AnalyteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private AnalyteController $analyteController;
    private string $perPage;
    private string $table;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(AnalytePermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('analyte.create');
        $role->givePermissionTo('analyte.update');
        $role->givePermissionTo('analyte.delete');
        $role->givePermissionTo('analyte.index');
        $role->givePermissionTo('analyte.show');

        $user->assignRole($role);

        $modelClass = new Analyte();
        $this->analyteController = new AnalyteController();

        $this->user = $user;
        $this->role = $role;
        $this->model = Analyte::factory()
            ->hasAttached(SamplingCondition::factory()->count(5), ['user_id' => $user->id])
            ->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }


    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {

        Analyte::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $countModels = Analyte::count();

        $response->assertJson(function (AssertableJson $json) use ($countModels) {
            return $json
                ->has('_links')
                ->has('count')
                ->has('collection', $countModels, function ($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'slug' => 'string',
                        'name' => 'string',
                        'active' => 'boolean',
                        '_links' => 'array'
                    ]);
                });
        });
    }

    public function test_se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        Analyte::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s?page=1', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $page = $this->perPage;

        $response->assertJson(function (AssertableJson $json) use ($page) {
            return $json
                ->has('links')
                ->has('meta')
                ->has('data.collection', $page, function ($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'slug' => 'string',
                        'name' => 'string',
                        'active' => 'boolean',
                        '_links' => 'array'
                    ]);
                });
        });
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void //show
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/{$this->table}/{$this->model->id}");

        $response->assertStatus(Response::HTTP_OK);

        $response->dump();

        $response
            ->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
                ->where('name', $this->model->name)
                ->where('clinical_information', $this->model->clinical_information)
                ->where('is_patient_codable', $this->model->is_patient_codable)
                ->where('active', $this->model->active)
                ->etc()
            );
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {

        $availability = Disponibility::factory()->create();
        $workarea = Workarea::factory()->create();
        $processTime = ProcessTime::factory()->create();
        $medicalRequestType = MedicalRequestType::factory()->create();
        $user = User::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'clinical_information' => $this->faker->text,
            'loinc_id' => $this->faker->slug,
            'workarea_id' => $availability->id,
            'availability_id' => $workarea->id,
            'process_time_id' => $processTime->id,
            'medical_request_type_id' => $medicalRequestType->id,
            'created_user_id' => $user->id,
            'is_patient_codable' => $this->faker->boolean,
            'active' => $this->faker->boolean
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}", $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response
            ->assertJson(fn(AssertableJson $json) => $json->where('name', $factoryModel['name'])
                ->where('clinical_information', $factoryModel['clinical_information'])
                ->where('is_patient_codable', $factoryModel['is_patient_codable'])
                ->where('active', $factoryModel['active'])
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'name' => $factoryModel['name'],
        ]);
    }

    public function test_se_puede_modificar_un_recurso(): void // update
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id), [
                'name' => 'new analyte modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response
            ->assertJson(fn(AssertableJson $json) => $json->where('name', 'new analyte modificado')
                ->where('clinical_information', $this->model->clinical_information)
                ->where('is_patient_codable', $this->model->is_patient_codable)
                ->where('active', $this->model->active)
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'name' => 'new analyte modificado'
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id' => $this->model->id]);
        $this->assertSoftDeleted($this->model);

    }


    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {

        $availability = Disponibility::factory()->create();
        $workarea = Workarea::factory()->create();
        $processTime = ProcessTime::factory()->create();
        $medicalRequestType = MedicalRequestType::factory()->create();
        $user = User::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'clinical_information' => $this->faker->text,
            'loinc_id' => $this->faker->slug,
            'workarea_id' => $availability->id,
            'availability_id' => $workarea->id,
            'process_time_id' => $processTime->id,
            'medical_request_type_id' => $medicalRequestType->id,
            'created_user_id' => $user->id,
            'is_patient_codable' => $this->faker->boolean,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('analyte.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}", $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'name' => $factoryModel['name'],
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('analyte.update');

        $url = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url, [
                'name' => 'laboratory name modificado'
            ]);

        $this->assertDatabaseMissing($this->table, [
            'name' => 'laboratory name modificado',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('analyte.delete');

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'name' => $this->model->name,
        ]);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }


    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, 'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Analyte::factory()->count(20)->create();

        $list = Analyte::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s&paginate=%s', $this->table, $i, $DEFAULT_PAGINATE))
                ->assertStatus(Response::HTTP_OK);

            if ($i < $pages) {
                $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }

            }
            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'slug' => 'string',
                            'name' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Analyte::factory()->count(20)->create();

        $list = Analyte::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s', $this->table, $i))
                ->assertStatus(Response::HTTP_OK);

            if ($i < $pages) {
                $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }
            }

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'slug' => 'string',
                            'name' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }

}

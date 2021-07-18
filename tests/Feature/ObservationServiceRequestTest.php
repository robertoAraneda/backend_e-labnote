<?php

namespace Tests\Feature;

use App\Http\Controllers\ObservationServiceRequestController;
use App\Models\Analyte;
use App\Models\Availability;
use App\Models\Container;
use App\Models\Laboratory;
use App\Models\Loinc;
use App\Models\MedicalRequestType;
use App\Models\ProcessTime;
use App\Models\Role;
use App\Models\Specimen;
use App\Models\User;
use App\Models\ObservationServiceRequest;
use App\Models\Workarea;
use Database\Seeders\ObservationServiceRequestPermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ObservationServiceRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private ObservationServiceRequestController $controller;
    private string $perPage;
    private string $table;
    private string $BASE_URI;

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(ObservationServiceRequestPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('observationServiceRequest.create');
        $role->givePermissionTo('observationServiceRequest.update');
        $role->givePermissionTo('observationServiceRequest.delete');
        $role->givePermissionTo('observationServiceRequest.index');
        $role->givePermissionTo('observationServiceRequest.show');

        $user->assignRole($role);

        $modelClass = new ObservationServiceRequest();
        $this->controller = new ObservationServiceRequestController();

        $this->user = $user;
        $this->role = $role;
        $this->model = ObservationServiceRequest::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();
        $this->BASE_URI = "/api/v1/observation-service-requests";
    }

    /**
     * @test
     */
    public function se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_del_recurso(): void
    {

        ObservationServiceRequest::factory()->count(20)->create();

        $uri = $this->BASE_URI;

        $countModels = ObservationServiceRequest::count();


        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);
        $response->assertStatus(Response::HTTP_OK);
        $response->dump();
        $response->assertJson(function (AssertableJson $json) use ($countModels) {
            return $json
                ->has('_links')
                ->has('count')
                ->has('collection', $countModels, function ($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'name' => 'string',
                        'active' => 'boolean',
                        '_links' => 'array'
                    ])->etc();
                });
        });
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        ObservationServiceRequest::factory()->count(20)->create();

        $uri = sprintf('/%s?page=1', $this->BASE_URI);
        $page = $this->perPage;

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) use ($page) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection', $page, function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
    }

    /**
     * @test
     */
    public function se_puede_obtener_el_detalle_del_recurso(): void //show
    {
        $this->withoutExceptionHandling();
        $uri = sprintf("%s/%s", $this->BASE_URI, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
                ->where('clinical_information', $this->model->clinical_information)
                ->where('active', $this->model->active)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void //store
    {
        $container = Container::factory()->create();
        $specimen = Specimen::factory()->create();
        $availability = Availability::factory()->create();
        $laboratory = Laboratory::factory()->create();
        $loinc = Loinc::factory()->create();
        $analyte = Analyte::factory()->create();
        $workarea = Workarea::factory()->create();
        $processTime = ProcessTime::factory()->create();
        $medicalRequestType = MedicalRequestType::factory()->create();

        $factoryModel = [
            'clinical_information' => $this->faker->text,
            'container_id' => $container->id,
            'specimen_id' => $specimen->id,
            'availability_id' => $availability->id,
            'laboratory_id' => $laboratory->id,
            'loinc_num' => $loinc->loinc_num,
            'analyte_id' => $analyte->id,
            'workarea_id' => $workarea->id,
            'process_time_id' => $processTime->id,
            'medical_request_type_id' => $medicalRequestType->id,
            'active' => $this->faker->boolean
        ];

        $uri = $this->BASE_URI;

        $response = $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('active', $factoryModel['active'])
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'clinical_information' => $factoryModel['clinical_information'],
        ]);
    }


    /**
     * @test
     */
    public function se_puede_modificar_un_recurso(): void // update
    {

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'clinical_information' => 'clinical_information modificado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('id', $this->model->id)
                ->where('clinical_information', 'clinical_information modificado')
                ->where('active', $this->model->active)
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'clinical_information' => 'clinical_information modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_puede_eliminar_un_recurso(): void //destroy
    {

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id' => $this->model->id]);
        $this->assertSoftDeleted($this->model);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {

        $container = Container::factory()->create();
        $specimen = Specimen::factory()->create();
        $availability = Availability::factory()->create();
        $laboratory = Laboratory::factory()->create();
        $loinc = Loinc::factory()->create();
        $analyte = Analyte::factory()->create();
        $workarea = Workarea::factory()->create();
        $processTime = ProcessTime::factory()->create();
        $medicalRequestType = MedicalRequestType::factory()->create();

        $factoryModel = [
            'clinical_information' => $this->faker->text,
            'container_id' => $container->id,
            'specimen_id' => $specimen->id,
            'availability_id' => $availability->id,
            'laboratory_id' => $laboratory->id,
            'loinc_num' => $loinc->loinc_num,
            'analyte_id' => $analyte->id,
            'workarea_id' => $workarea->id,
            'process_time_id' => $processTime->id,
            'medical_request_type_id' => $medicalRequestType->id,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('observationServiceRequest.create');

        $uri = $this->BASE_URI;

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'clinical_information' => $factoryModel['clinical_information'],
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('observationServiceRequest.update');

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, [
                'clinical_information' => 'resource modificado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'clinical_information' => 'resource modificado'
        ]);
    }

    /**
     *
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('observationServiceRequest.delete');

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'clinical_information' => $this->model->clinical_information,
        ]);
    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('%s/%s', $this->BASE_URI, -5);
        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', $this->BASE_URI, -5);

        $this->actingAs($this->user, 'api')
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', $this->BASE_URI, -5);

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {
        ObservationServiceRequest::factory()->count(20)->create();
        $list = ObservationServiceRequest::count();
        $DEFAULT_PAGINATE = 5;
        $mod = $list % $DEFAULT_PAGINATE;
        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $uri = sprintf('%s?page=%s&paginate=%s', $this->BASE_URI, $i, $DEFAULT_PAGINATE);
            $response = $this->actingAs($this->user, 'api')
                ->getJson($uri)
                ->assertStatus(Response::HTTP_OK)
                ->assertJson(function (AssertableJson $json) {
                    return $json
                        ->has('links')
                        ->has('meta')
                        ->has('data.collection.0', function ($json) {
                            $json->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                                'active' => 'boolean',
                                '_links' => 'array'
                            ]);
                        });
                });

            if ($i < $pages) {
                $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }

            }
        }
        $this->assertDatabaseCount($this->table, $list);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        ObservationServiceRequest::factory()->count(20)->create();

        $list = ObservationServiceRequest::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('%s?page=%s', $this->BASE_URI, $i);

            $response = $this
                ->actingAs($this->user, 'api')
                ->getJson($uri)
                ->assertStatus(Response::HTTP_OK)
                ->assertJson(function (AssertableJson $json) {
                    return $json
                        ->has('links')
                        ->has('meta')
                        ->has('data.collection.0', function ($json) {
                            $json->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                                'active' => 'boolean',
                                '_links' => 'array'
                            ]);
                        });
                });

            if ($i < $pages) {
                $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }
            }
        }

        $this->assertDatabaseCount($this->table, $list);
    }
}

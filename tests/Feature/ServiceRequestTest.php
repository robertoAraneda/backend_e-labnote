<?php

namespace Tests\Feature;

use App\Models\AddressPatient;
use App\Models\AdministrativeGender;
use App\Models\ContactPatient;
use App\Models\ContactPointPatient;
use App\Models\HumanName;
use App\Models\IdentifierPatient;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestIntent;
use App\Models\ServiceRequestPriority;
use App\Models\ServiceRequestStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ServiceRequestPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;

    private const BASE_URI = '/api/v1/service-requests';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(ServiceRequestPermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('serviceRequest.create');
        $role->givePermissionTo('serviceRequest.update');
        $role->givePermissionTo('serviceRequest.delete');
        $role->givePermissionTo('serviceRequest.index');
        $role->givePermissionTo('serviceRequest.show');

        $user->assignRole($role);

        $modelClass = new ServiceRequest();

        $this->user = $user;
        $this->role = $role;
        $this->model = ServiceRequest::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = 'service_requests';

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        ServiceRequest::factory()->count(20)->create();

        $uri = sprintf('%s', self::BASE_URI);
        $countModels = ServiceRequest::count();

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function (AssertableJson $json) use ($countModels) {
            return $json
                ->has('_links')
                ->has('count')
                ->has('collection', $countModels, function ($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'note' => 'string',
                        'requisition' => 'string',
                        'occurrence' => 'string',
                        '_links' => 'array'
                    ]);
                });
        });

    }

    public function test_se_puede_obtener_una_lista_paginada_del_recurso(): void
    {
        ServiceRequest::factory()->count(20)->create();

        $uri = sprintf('%s?page=1', self::BASE_URI);
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
                            'note' => 'string',
                            'requisition' => 'string',
                            'occurrence' => 'string',
                            '_links' => 'array'
                        ]);
                    });
            });
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void //show
    {

        $this->withoutExceptionHandling();
        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
            ->where('note', $this->model->note)
            ->where('requisition', $this->model->requisition)
            ->etc()
        );
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {
        $patient = Patient::factory()
            ->has(AdministrativeGender::factory())
            ->has(IdentifierPatient::factory())
            ->has(HumanName::factory())
            ->has(AddressPatient::factory())
            ->has(ContactPointPatient::factory())
            ->has(ContactPatient::factory())->create();
        $status = ServiceRequestStatus::factory()->create();
        $intent = ServiceRequestIntent::factory()->create();
        $priority = ServiceRequestPriority::factory()->create();
        $category = ServiceRequestCategory::factory()->create();
        $requester = Practitioner::factory()->create();
        $performer = Practitioner::factory()->create();
        $location = Location::factory()->create();

        $factoryModel = [
            'requisition' => "23456745",
            'note' => $this->faker->text,
            'service_request_status_id' => $status->id,
            'service_request_intent_id' => $intent->id,
            'service_request_priority_id' => $priority->id,
            'service_request_category_id' => $category->id,
            'patient_id' => $patient->id,
            'requester_id' => $requester->id,
            'performer_id' => $performer->id,
            'location_id' => $location->id,
        ];

        $uri = sprintf("%s", self::BASE_URI);

        $response = $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('requisition', $factoryModel['requisition'])
            ->where('note', $factoryModel['note'])
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'requisition' => $factoryModel['requisition'],
        ]);
    }

    public function test_se_puede_modificar_un_recurso(): void // update
    {
        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'note' => 'name modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('id', $this->model->id)
            ->where('note', 'name modificado')
            ->where('requisition', $this->model->requisition)
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'note' => 'name modificado'
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {
        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id' => $this->model->id]);
        $this->assertSoftDeleted($this->model);

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $patient = Patient::factory()
            ->has(AdministrativeGender::factory())
            ->has(IdentifierPatient::factory())
            ->has(HumanName::factory())
            ->has(AddressPatient::factory())
            ->has(ContactPointPatient::factory())
            ->has(ContactPatient::factory())->create();
        $status = ServiceRequestStatus::factory()->create();
        $intent = ServiceRequestIntent::factory()->create();
        $priority = ServiceRequestPriority::factory()->create();
        $category = ServiceRequestCategory::factory()->create();
        $requester = Practitioner::factory()->create();
        $performer = Practitioner::factory()->create();
        $location = Location::factory()->create();

        $factoryModel = [
            'requisition' => "23456745",
            'note' => $this->faker->text,
            'service_request_status_id' => $status->id,
            'service_request_intent_id' => $intent->id,
            'service_request_priority_id' => $priority->id,
            'service_request_category_id' => $category->id,
            'patient_id' => $patient->id,
            'requester_id' => $requester->id,
            'performer_id' => $performer->id,
            'location_id' => $location->id,
        ];

        $this->role->revokePermissionTo('serviceRequest.create');

        $uri = sprintf('%s', self::BASE_URI);

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'requisition' => $factoryModel['requisition'],
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('serviceRequest.update');

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, [
                'note' => 'resource modificado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'note' => 'resource modificado'
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('serviceRequest.delete');

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'requisition' => $this->model->requisition,
        ]);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('%s/%s', self::BASE_URI, -5);
        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $this->actingAs($this->user, 'api')
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, 'string');

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        ServiceRequest::factory()->count(20)->create();

        $list = ServiceRequest::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('%s?page=%s&paginate=%s',self::BASE_URI, $i, $DEFAULT_PAGINATE))
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
                            'requisition' => 'string',
                            'note' => 'string',
                            'occurrence' => 'string',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);

    }


    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        ServiceRequest::factory()->count(20)->create();

        $list = ServiceRequest::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('%s?page=%s', self::BASE_URI, $i);

            $response = $this
                ->actingAs($this->user, 'api')
                ->getJson($uri)
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
                            'requisition' => 'string',
                            'note' => 'string',
                            'occurrence' => 'string',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }
}

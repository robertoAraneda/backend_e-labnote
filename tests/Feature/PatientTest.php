<?php

namespace Tests\Feature;

use App\Models\AddressPatient;
use App\Models\City;
use App\Models\ContactPatient;
use App\Models\ContactPointPatient;
use App\Models\District;
use App\Models\AdministrativeGender;
use App\Models\HumanName;
use App\Models\IdentifierPatient;
use App\Models\IdentifierType;
use App\Models\IdentifierUse;
use App\Models\Patient;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Database\Seeders\PatientPermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;

    const BASE_URI = '/api/v1/patients';

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(PatientPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('patient.create');
        $role->givePermissionTo('patient.update');
        $role->givePermissionTo('patient.delete');
        $role->givePermissionTo('patient.index');
        $role->givePermissionTo('patient.show');

        $user->assignRole($role);

        $modelClass = new Patient();

        $this->user = $user;
        $this->role = $role;
        $this->model = Patient::factory()
            ->has(AdministrativeGender::factory())
            ->has(IdentifierPatient::factory())
            ->has(HumanName::factory())
            ->has(AddressPatient::factory())
            ->has(ContactPointPatient::factory())
            ->has(ContactPatient::factory())
            ->create();

        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

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

        Patient::factory()->has(AdministrativeGender::factory())->count(20)->create();

        $uri = sprintf('/api/v1/%s', $this->table);
        $countModels = Patient::count();

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
                        'birthdate' => 'string',
                        'active' => 'boolean',
                        '_links' => 'array'
                    ]);
                });
        });

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        Patient::factory()->has(AdministrativeGender::factory())->count(20)->create();

        $uri = sprintf('/api/v1/%s?page=1', $this->table);
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
        $uri = sprintf("/api/v1/%s/%s", $this->table, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
                ->where('birthdate', $this->model->birthdate)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void //store
    {

        $gender = AdministrativeGender::factory()->create();
        $city = City::factory()->create();
        $state = State::factory()->create();
        $identifierType = IdentifierType::factory()->create();
        $identifierUse = IdentifierUse::factory()->create();

        $factoryModel = [
            'patient' => [
                'birthdate' => $this->faker->date(),
                'administrative_gender_id' => $gender->id,
                'active' => $this->faker->boolean
            ],
            'identifierPatient' => [
                [
                    'identifier_use_id' => $identifierType->id,
                    'identifier_type_id' => $identifierUse->id,
                    'value' => $this->faker->lastName
                ]
            ],
            'humanName' => [
                'use' => 'usual',
                'given' => 'Roberto Alejandro',
                'father_family' => 'Araneda',
                'mother_family' => 'Espinoza'
            ],
            'contactPointPatient' => [
                [
                    'system' => 'email',
                    'value' => 'roberto.aranedaespinoza@minsal.cl',
                    'use' => 'work'],
                [
                    'system' => 'phone',
                    'value' => '+56452556789',
                    'use' => 'work'],
                [
                    'system' => 'sms',
                    'value' => '+56958639620',
                    'use' => 'work']
            ],
            'addressPatient' => [
                [
                    'use' => 'home',
                    'text' => 'Juan Enrique Rodo 05080',
                    'city_id' => $city->id,
                    'state_id' => $state->id],
                [
                    'use' => 'work',
                    'text' => 'Huerfanos 670',
                    'city_id' => $city->id,
                    'state_id' => $state->id],
            ],
            'contactPatient' => [
                [
                    'given' => 'Yolanda',
                    'family' => 'Espinoza',
                    'relationship' => 'Padre',
                    'phone' => '+56958639620',
                    'email' => 'yolanda@gmail.com'
                ]
            ]
        ];

        $uri = sprintf("/api/v1/%s", $this->table);

        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);


        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('birthdate', $factoryModel['patient']['birthdate'])
            ->where('active', $factoryModel['patient']['active'])
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'birthdate' => $factoryModel['patient']['birthdate'],
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_el_nombre_del_paciente(): void // update
    {

        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => '2021-01-01',
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'humanName' => [
                'id' => $this->model->humanNames[0]->id,
                'use' => 'official',
                'given' => 'Roberto Alejandro',
                'father_family' => 'Araneda',
                'mother_family' => 'Espinoza'
            ]
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('birthdate', '2021-01-01')
            ->where('name.0.father_family', 'Araneda')
            ->where('name.0.mother_family', 'Espinoza')
            ->where('name.0.given', 'Roberto Alejandro')
            ->where('name.0.use', 'official')
            ->etc()
        );

        $this->assertDatabaseHas('human_names', [
            'given' => 'Roberto Alejandro',
            'father_family' => 'Araneda',
            'mother_family' => 'Espinoza',
            'patient_id' => $this->model->id
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_el_identificador_del_paciente(): void // update
    {

        $identifierType = IdentifierType::create(['code' => 'RUT', 'display' => 'RUT']);
        $identifierUse = IdentifierUse::create(['code' => 'official', 'display' => 'Oficial']);

        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => $this->model->birthdate,
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'identifierPatient' => [
                [
                    'id' => $this->model->identifierPatient[0]->id,
                    'identifier_use_id' => $identifierType->id,
                    'identifier_type_id' => $identifierUse->id,
                    'value' => '15654738-7'
                ]
            ],
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('identifier.0.value', '15654738-7')
            ->where('identifier.0.type', 'RUT')
            ->where('identifier.0.use', 'Oficial')
            ->etc()
        );

        $this->assertDatabaseHas('identifier_patients', [
            'value' => '15654738-7',
            'patient_id' => $this->model->id
        ]);
    }


    /**
     * @test
     */
    public function se_puede_modificar_la_direccion_del_paciente(): void // update
    {

        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => $this->model->birthdate,
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'addressPatient' =>
                [
                    'id' => $this->model->addressPatient[0]->id,
                    'use' => 'home',
                    'text' => 'Llutay 1968'
                ]
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('address.0.use', 'home')
            ->where('address.0.text', 'Llutay 1968')
            ->etc()
        );

        $this->assertDatabaseHas('address_patients', [
            'text' => 'Llutay 1968',
            'use' => 'home',
            'patient_id' => $this->model->id
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_un_punto_de_contacto_del_paciente(): void // update
    {

        $this->withoutExceptionHandling();
        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => $this->model->birthdate,
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'contactPointPatient' => [
                [
                    'id' => $this->model->contactPointPatient[0]->id,
                    'system' => 'email',
                    'value' => 'robaraneda@gmail.com',
                    'use' => 'work'
                ]
            ]
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('telecom.0.system', 'email')
            ->where('telecom.0.value', 'robaraneda@gmail.com')
            ->where('telecom.0.use', 'work')
            ->etc()
        );

        $this->assertDatabaseHas('contact_point_patients', [
            'value' => 'robaraneda@gmail.com',
            'system' => 'email',
            'use' => 'work',
            'patient_id' => $this->model->id
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_el_contacto_del_paciente(): void // update
    {

        $this->withoutExceptionHandling();

        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => $this->model->birthdate,
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'contactPatient' => [
                [
                    'id' => $this->model->contactPatient[0]->id,
                    'given' => 'Yolanda',
                    'family' => 'Espinoza',
                    'relationship' => 'Padre',
                    'phone' => '+56958639620',
                    'email' => 'yolanda@gmail.com'
                ]
            ]
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('contact.0.given', 'Yolanda')
            ->where('contact.0.family', 'Espinoza')
            ->where('contact.0.relationship', 'Padre')
            ->where('contact.0.phone', '+56958639620')
            ->where('contact.0.email', 'yolanda@gmail.com')
            ->etc()
        );

        $this->assertDatabaseHas('contact_patients', [
            'given' => 'Yolanda',
            'family' => 'Espinoza',
            'relationship' => 'Padre',
            'phone' => '+56958639620',
            'email' => 'yolanda@gmail.com',
            'patient_id' => $this->model->id
        ]);
    }

    /**
     * @test
     */
    public function se_puede_eliminar_un_recurso(): void //destroy
    {

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('human_names', ['patient_id' => $this->model->id]);

        $this->assertSoftDeleted($this->model);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {

        $administrativeGender = AdministrativeGender::factory()->create();
        $city = City::factory()->create();
        $state = State::factory()->create();

        $factoryModel = [
            'patient' => [
                'birthdate' => $this->faker->date(),
                'administrative_gender_id' => $administrativeGender->id,
                'active' => $this->faker->boolean
            ],
            'humanName' => [
                'use' => 'usual',
                'given' => 'Roberto Alejandro',
                'father_family' => 'Araneda',
                'mother_family' => 'Espinoza'
            ],
            'contactPointPatient' => [
                [
                    'system' => 'email',
                    'value' => 'roberto.aranedaespinoza@minsal.cl',
                    'use' => 'work'],
                [
                    'system' => 'phone',
                    'value' => '+56452556789',
                    'use' => 'work'],
                [
                    'system' => 'sms',
                    'value' => '+56958639620',
                    'use' => 'work']
            ],
            'addressPatient' => [
                [
                    'use' => 'home',
                    'text' => 'Juan Enrique Rodo 05080',
                    'city_id' => $city->id,
                    'state_id' => $state->id],
                [
                    'use' => 'work',
                    'text' => 'Huerfanos 670',
                    'city_id' => $city->id,
                    'state_id' => $state->id],
            ],
            'contactPatient' => [
                [
                    'given' => 'Yolanda',
                    'family' => 'Espinoza',
                    'relationship' => 'Padre',
                    'phone' => '+56958639620',
                    'email' => 'yolanda@gmail.com'
                ]
            ]
        ];

        $this->role->revokePermissionTo('patient.create');

        $uri = sprintf('/api/v1/%s', $this->table);

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'birthdate' => $factoryModel['patient']['birthdate'],
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('patient.update');

        $factoryModifiedModel = [
            'patient' => [
                'birthdate' => '2021-01-01',
                'administrative_gender_id' => $this->model->administrative_gender_id,
                'active' => $this->model->active
            ],
            'humanName' => [
                'id' => $this->model->humanNames[0]->id,
                'use' => 'official',
                'given' => 'Roberto Alejandro',
                'father_family' => 'Araneda',
                'mother_family' => 'Espinoza'
            ]
        ];

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, $factoryModifiedModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'birthdate' => '2021-01-01'
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('patient.delete');

        $uri = sprintf('/api/v1/%s/%s', $this->table, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'birthdate' => $this->model->birthdate,
        ]);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);
        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);

        $this->actingAs($this->user, 'api')
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, -5);

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('/api/v1/%s/%s', $this->table, 'string');

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Patient::factory()->count(4)->create();

        $list = Patient::count();

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
                            'birthdate' => 'string',
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
        Patient::factory()->count(20)->create();

        $list = Patient::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('/api/v1/%s?page=%s', $this->table, $i);

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
                            'birthdate' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }
}

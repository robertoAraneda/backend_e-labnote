<?php

namespace Tests\Feature;

use App\Models\AdministrativeGender;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdministrativeGenderPermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdministrativeGenderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;

    const BASE_URI = '/api/v1/administrative-genders';

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(AdministrativeGenderPermissionSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $user->assignRole($role);

        $modelClass = new AdministrativeGender();

        $this->user = $user;
        $this->role = $role;
        $this->model = AdministrativeGender::factory()->create();
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

        $this->withoutExceptionHandling();

        AdministrativeGender::factory()->count(20)->create();

        $uri = sprintf('%s', self::BASE_URI);
        $countModels = AdministrativeGender::count();

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) use ($countModels) {
                return $json
                    ->has('_links')
                    ->has('count')
                    ->has('collection', $countModels, function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'display' => 'string',
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

        AdministrativeGender::factory()->count(20)->create();

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
                            'display' => 'string',
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

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
            ->where('display', $this->model->display)
            ->where('code', $this->model->code)
            ->etc()
        );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void //store
    {
        $district = AdministrativeGender::factory()->create();
        $factoryModel = [
            'display' => $this->faker->name,
            'code' => $this->faker->title,
            'district_id' => $district->id,
            'active' => $this->faker->boolean
        ];

        $uri = sprintf("%s", self::BASE_URI);

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('display', $factoryModel['display'])
                ->where('code', $factoryModel['code'])
                ->where('active', $factoryModel['active'])
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'display' => $factoryModel['display'],
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_un_recurso(): void // update
    {

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'display' => 'name modificado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('id', $this->model->id)
                ->where('display', 'name modificado')
                ->where('code', $this->model->code)
                ->where('active', $this->model->active)
                ->etc()
            );


        $this->assertDatabaseHas($this->table, [
            'display' => 'name modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_puede_eliminar_un_recurso(): void //destroy
    {

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

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

        $district = AdministrativeGender::factory()->create();

        $factoryModel = [
            'display' => $this->faker->slug,
            'code' => $this->faker->title,
            'district_id' => $district->id,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('administrativeGender.create');

        $uri = sprintf('%s', self::BASE_URI);

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'display' => $factoryModel['display'],
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('administrativeGender.update');

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, [
                'display' => 'resource modificado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'display' => 'resource modificado'
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('administrativeGender.delete');

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'display' => $this->model->display,
        ]);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('%s/%s', self::BASE_URI, -5);
        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $this->actingAs($this->user, 'api')
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
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

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        AdministrativeGender::factory()->count(20)->create();

        $list = AdministrativeGender::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('%s?page=%s&paginate=%s', self::BASE_URI, $i, $DEFAULT_PAGINATE))
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
                            'display' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        AdministrativeGender::factory()->count(20)->create();

        $list = AdministrativeGender::count();

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
                            'display' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }

    /**
     * @test
     */
    public function se_puede_modificar_el_estado_de_un_recurso()
    {

        $uri = sprintf('%s/%s/status', self::BASE_URI, $this->model->id);

        if ($this->model->active) {
            $response = $this->actingAs($this->user, 'api')
                ->putJson($uri, [
                    'active' => false
                ]);
        } else {
            $response = $this->actingAs($this->user, 'api')
                ->putJson($uri, [
                    'active' => true
                ]);
        }

        $response->assertStatus(Response::HTTP_OK);

        $this->assertNotEquals($response['active'], $this->model->active);

    }
}

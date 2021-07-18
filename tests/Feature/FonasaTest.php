<?php

namespace Tests\Feature;

use App\Http\Controllers\FonasaController;
use App\Models\Fonasa;
use App\Models\Role;
use App\Models\Specimen;
use App\Models\User;
use Database\Seeders\FonasaPermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FonasaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private FonasaController $fonasaController;
    private string $table;
    private string $perPage;
    const  BASE_URI = '/api/v1/fonasas';

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(FonasaPermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('fonasa.create');
        $role->givePermissionTo('fonasa.update');
        $role->givePermissionTo('fonasa.delete');
        $role->givePermissionTo('fonasa.index');
        $role->givePermissionTo('fonasa.show');

        $user->assignRole($role);

        $modelClass = new Fonasa();

        $this->user = $user;
        $this->role = $role;
        $this->model = Fonasa::factory()->create();
        $this->table = $modelClass->getTable();
        $this->perPage = $modelClass->getPerPage();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        Fonasa::factory()->count(20)->create();

        $countModels = Fonasa::count();

        $this->actingAs($this->user, 'api')
            ->getJson(self::BASE_URI)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) use ($countModels) {
                return $json
                    ->has('_links')
                    ->has('count')
                    ->has('collection', $countModels, function ($json) {
                        $json->whereAllType([
                            'name' => 'string',
                            'mai_code' => 'string',
                            'rem_code' => 'string',
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

        Fonasa::factory()->count(20)->create();

        $uri = sprintf('/%s?page=1', self::BASE_URI);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'rem_code' => 'string',
                            'mai_code' => 'string',
                            'name' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void //show
    {

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->mai_code);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->where('mai_code', $this->model->mai_code)
            ->where('name', $this->model->name)
            ->where('active', $this->model->active)
            ->etc()
        );
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {

        $factoryModel = [
            'name' => $this->faker->name,
            'mai_code' => $this->faker->word,
            'rem_code' => $this->faker->word,
            'active' => $this->faker->boolean
        ];

        $response = $this
            ->actingAs($this->user, 'api')
            ->postJson(self::BASE_URI, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', $factoryModel['name'])
            ->where('mai_code', $factoryModel['mai_code'])
            ->where('rem_code', $factoryModel['rem_code'])
            ->where('active', $factoryModel['active'])
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'mai_code' => $factoryModel['mai_code'],
        ]);
    }

    public function test_se_puede_modificar_un_recurso(): void // update
    {

        $uri = sprintf('%s/%s',self::BASE_URI, $this->model->mai_code);
        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'name' => 'new fonasa modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', 'new fonasa modificado')
            ->where('active', $this->model->active)
            ->etc()
        );
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {
        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->mai_code);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['mai_code' => $this->model->mai_code]);
        $this->assertSoftDeleted($this->model);
    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {

        $factoryModel = [
            'name' => $this->faker->name,
            'mai_code' => $this->faker->word,
            'rem_code' => $this->faker->word,
            'active' => true
        ];

        $this->role->revokePermissionTo('fonasa.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson(self::BASE_URI, $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'mai_code' => $factoryModel['mai_code'],
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('fonasa.update');


        $uri = sprintf('%s/%s',self::BASE_URI, $this->model->mai_code);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, [
                'name' => 'resource modificado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'name' => 'resource modificado'
        ]);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('fonasa.delete');

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->mai_code);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'name' => $this->model->name,
        ]);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }


    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {
        Fonasa::factory()->count(20)->create();
        $list = Fonasa::count();
        $DEFAULT_PAGINATE = 5;
        $mod = $list % $DEFAULT_PAGINATE;
        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $uri = sprintf('%s?page=%s&paginate=%s', self::BASE_URI, $i, $DEFAULT_PAGINATE);
            $response = $this->actingAs($this->user, 'api')
                ->getJson($uri)
                ->assertStatus(Response::HTTP_OK)
                ->assertJson(function (AssertableJson $json) {
                    return $json
                        ->has('links')
                        ->has('meta')
                        ->has('data.collection.0', function ($json) {
                            $json->whereAllType([
                                'rem_code' => 'string',
                                'mai_code' => 'string',
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
        Fonasa::factory()->count(20)->create();

        $list = Fonasa::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('%s?page=%s', self::BASE_URI, $i);

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
                                'rem_code' => 'string',
                                'mai_code' => 'string',
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


    /**
     * @test
     */
    public function se_puede_modificar_el_estado_de_un_recurso()
    {

        $this->withoutExceptionHandling();
        $uri = sprintf('%s/%s/status', self::BASE_URI, $this->model->mai_code);

        if($this->model->active){
            $response = $this->actingAs($this->user, 'api')
                ->putJson($uri, [
                    'active' => false
                ]);
        }else{
            $response = $this->actingAs($this->user, 'api')
                ->putJson($uri, [
                    'active' => true
                ]);
        }

        $response->assertStatus(Response::HTTP_OK);

        $this->assertNotEquals($response['active'], $this->model->active);

    }


}

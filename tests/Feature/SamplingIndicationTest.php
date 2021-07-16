<?php

namespace Tests\Feature;

use App\Http\Controllers\SamplingIndicationController;
use App\Models\Role;
use App\Models\SamplingIndication;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SamplingIndicationPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SamplingIndicationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private SamplingIndicationController $samplingIndicationController;
    private string $perPage;
    private string $table;
    private string $BASE_URI;

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(SamplingIndicationPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('samplingIndication.create');
        $role->givePermissionTo('samplingIndication.update');
        $role->givePermissionTo('samplingIndication.delete');
        $role->givePermissionTo('samplingIndication.index');
        $role->givePermissionTo('samplingIndication.show');

        $user->assignRole($role);

        $modelClass = new SamplingIndication();
        $this->samplingIndicationController = new SamplingIndicationController();

        $this->user = $user;
        $this->role = $role;
        $this->model = SamplingIndication::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();
        $this->BASE_URI = "/api/v1/sampling-indications";
    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_del_recurso(): void
    {

        SamplingIndication::factory()->count(20)->create();

        $uri = $this->BASE_URI;

        $countModels = SamplingIndication::count();

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
    public function se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        SamplingIndication::factory()->count(20)->create();

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
                ->where('name', $this->model->name)
                ->where('active', $this->model->active)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void //store
    {
        $factoryModel = [
            'name' => $this->faker->name,
            'active' => $this->faker->boolean
        ];

        $uri = $this->BASE_URI;

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('name', $factoryModel['name'])
                ->where('active', $factoryModel['active'])
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'name' => $factoryModel['name'],
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
                'name' => 'name modificado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('id', $this->model->id)
                ->where('name', 'name modificado')
                ->where('active', $this->model->active)
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'name' => 'name modificado'
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

        $factoryModel = [
            'name' => $this->faker->slug,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('samplingIndication.create');

        $uri = $this->BASE_URI;

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'name' => $factoryModel['name'],
        ]);

    }

    /**
     * @test
     */

    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('samplingIndication.update');

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

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

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('samplingIndication.delete');

        $uri = sprintf('%s/%s', $this->BASE_URI, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'name' => $this->model->name,
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
        SamplingIndication::factory()->count(20)->create();
        $list = SamplingIndication::count();
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
        SamplingIndication::factory()->count(20)->create();
        $list = SamplingIndication::count();
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

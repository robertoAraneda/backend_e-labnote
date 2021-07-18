<?php

namespace Tests\Feature;

use App\Http\Controllers\SampleQuantityController;
use App\Models\Role;
use App\Models\SampleQuantity;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleQuantityPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SampleQuantityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private SampleQuantityController $sampleQuantityController;
    private string $table;
    private string $base_url;
    private string $perPage;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(SampleQuantityPermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('sampleQuantity.create');
        $role->givePermissionTo('sampleQuantity.update');
        $role->givePermissionTo('sampleQuantity.delete');
        $role->givePermissionTo('sampleQuantity.index');
        $role->givePermissionTo('sampleQuantity.show');

        $user->assignRole($role);

        $modelClass = new SampleQuantity();
        $this->sampleQuantityController = new SampleQuantityController();

        $this->user = $user;
        $this->role = $role;
        $this->model = SampleQuantity::factory()->create();
        $this->table = $modelClass->getTable();
        $this->base_url = '/api/v1/sample-quantities';
        $this->perPage = $modelClass->getPerPage();

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
        SampleQuantity::factory()->count(20)->create();

        $uri = sprintf('%s', $this->base_url);
        $countModels = SampleQuantity::count();

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
        SampleQuantity::factory()->count(20)->create();

        $uri = sprintf('%s?page=1', $this->base_url);
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
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
                ->where('name', $this->model->name)
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

        $uri = sprintf("%s", $this->base_url);

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
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

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
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

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
        $list = SampleQuantity::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'active' => true
        ];

        $this->role->revokePermissionTo('sampleQuantity.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson($this->base_url, $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('sampleQuantity.update');

        $url = sprintf('%s/%s',$this->base_url, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'sampleQuantity name modificado'
            ]);

        $this->assertNotEquals($this->model->name, 'sampleQuantity name modificado');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('sampleQuantity.delete');

        $list = SampleQuantity::count();

        $uri = sprintf('%s/%s',$this->base_url ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url , -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {
        SampleQuantity::factory()->count(20)->create();

        $list = SampleQuantity::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('%s?page=%s&paginate=%s', $this->base_url, $i, $DEFAULT_PAGINATE))
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
                            'name' => 'string',
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
        SampleQuantity::factory()->count(20)->create();

        $list = SampleQuantity::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('%s?page=%s', $this->base_url, $i);

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
                            'name' => 'string',
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
        $this->withoutExceptionHandling();
        $uri = sprintf('%s/%s/status', $this->base_url, $this->model->id);

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


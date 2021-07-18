<?php

namespace Tests\Feature;

use App\Http\Controllers\ModuleController;
use App\Models\Menu;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ModulePermissionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;
    const BASE_URI = '/api/v1/modules';

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(ModulePermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('module.create');
        $role->givePermissionTo('module.update');
        $role->givePermissionTo('module.delete');
        $role->givePermissionTo('module.index');
        $role->givePermissionTo('module.show');

        $modelClass = new Module;

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->model = Module::factory()->create();
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

        Module::factory()->count(10)->create();

        $uri = self::BASE_URI;

        $countModels = Module::count();

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
                        'name' => 'string',
                        'url' => 'string',
                        'icon' => 'string',
                        'slug' => 'string',
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

        Module::factory()->count(20)->create();

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
                        'id' => 'integer',
                        'name' => 'string',
                        'url' => 'string',
                        'icon' => 'string',
                        'slug' => 'string',
                        'active' => 'boolean',
                        '_links' => 'array'
                    ]);
                });
        });
    }

    /**
     * @test
     */
    public function se_puede_obtener_el_detalle_del_recurso(): void
    {

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('id', $this->model->id)
            ->where('name', $this->model->name)
            ->where('url', $this->model->url)
            ->where('icon', $this->model->icon)
            ->where('slug', $this->model->slug)
            ->where('active', $this->model->active)
            ->etc()
        );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void
    {
        $factoryModel = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'icon' => $this->faker->lastname,
            'slug' => $this->faker->slug,
            'active' => $this->faker->boolean
        ];

        $uri = self::BASE_URI;

        $response = $this->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', $factoryModel['name'])
            ->where('url', $factoryModel['url'])
            ->where('icon', $factoryModel['icon'])
            ->where('slug', $factoryModel['slug'])
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
    public function se_puede_modificar_un_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id), [
                'name' => 'resource modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('id', $this->model->id)
            ->where('name', 'resource modificado')
            ->where('url', $this->model->url)
            ->where('slug', $this->model->slug)
            ->where('active', $this->model->active)
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'name' => 'resource modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_puede_eliminar_un_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id' => $this->model->id]);
        $this->assertSoftDeleted($this->model);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $factoryModel = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'icon' => $this->faker->lastname,
            'slug' => $this->faker->slug,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('module.create');

        $uri = self::BASE_URI;

        $response = $this->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJsonFragment([
            'message' => 'This action is unauthorized.'
        ]);

        $this->assertDatabaseMissing($this->table, [
            'name' => $factoryModel['name'],
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('module.update');
        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'name' => 'resource modificado'
            ]);

        $response->assertJsonFragment([
            'message' => 'This action is unauthorized.'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'name' => 'resource modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('module.delete');

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);


        $this->assertDatabaseHas($this->table, [
            'name' => $this->model->name,
        ]);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
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

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s', self::BASE_URI, -5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_500_si_parametro_no_es_numerico_al_buscar(): void
    {

        $uri = sprintf('%s/%s', self::BASE_URI, 'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Module::factory()->count(20)->create();

        $list = Module::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for($i = 1; $i <= $pages; $i++){
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s&paginate=%s',$this->table , $i, $DEFAULT_PAGINATE ))
                ->assertStatus(Response::HTTP_OK);

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'url' => 'string',
                            'icon' => 'string',
                            'slug' => 'string',
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
        Module::factory()->count(20)->create();

        $list = Module::count();

        $pages = intval(ceil($list / $this->perPage ));
        $mod = $list % $this->perPage ;

        for($i = 1; $i <= $pages; $i++){

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s',$this->table ,$i))
                ->assertStatus(Response::HTTP_OK);

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'url' => 'string',
                            'icon' => 'string',
                            'slug' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });

            if($i < $pages){
                $this->assertEquals($this->perPage ,  collect($response['data']['collection'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($this->perPage ,  collect($response['data']['collection'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data']['collection'])->count());
                }
            }

        }

        $this->assertDatabaseCount($this->table, $list);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_de_menus_por_modulo(): void
    {
        $module = Module::factory()->create();
        Menu::factory()->count(20)->for($module)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s/%s/menus',$this->table , $module->id))
            ->assertStatus(Response::HTTP_OK);

    }

    /**
     * @test
     */
    public function se_puede_modificar_el_estado_de_un_recurso()
    {
        $status = filter_var($this->model->active, FILTER_VALIDATE_BOOLEAN);

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri,  [
                'active' => !$status,
            ]);

        $this->assertNotEquals($this->model->active, (bool) $response['active']);
    }
}

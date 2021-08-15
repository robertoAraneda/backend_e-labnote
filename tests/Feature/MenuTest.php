<?php

namespace Tests\Feature;

use App\Http\Controllers\MenuController;
use App\Models\Menu;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\MenuPermissionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;
    const BASE_URI = '/api/v1/menus';

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(MenuPermissionSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $modelClass = new Menu;

        $user->assignRole($role);

        $this->user = $user;
        $this->role = $role;
        $this->model = Menu::factory()->for(Module::factory()->create())->create();
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
        Menu::factory()->count(20)->create();

        $uri = self::BASE_URI;

        $countModels = Menu::count();

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
                        'order' => 'integer',
                        'icon' => 'string',
                        'module' => 'array',
                        'permission' => 'array',
                        'module_id' => 'integer',
                        'permission_id' => 'integer',
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

        Menu::factory()->count(20)->create();

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
                        'order' => 'integer',
                        'icon' => 'string',
                        'module' => 'array',
                        'permission' => 'array',
                        'module_id' => 'integer',
                        'permission_id' => 'integer',
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
            ->where('order', $this->model->order)
            ->where('icon', $this->model->icon)
            ->where('module_id', $this->model->module_id)
            ->where('permission_id', $this->model->permission_id)
            ->where('active', $this->model->active)
            ->etc()
        );

    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void
    {

        $module = Module::factory()->create();
        $permission = Permission::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'module_id' => $module->id,
            'permission_id' => $permission->id,
            'url' => $this->faker->url,
            'icon' => $this->faker->lastName,
            'order' => 1,
            'active' => $this->faker->boolean
        ];

        $uri = self::BASE_URI;

        $response = $this->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', $factoryModel['name'])
            ->where('order', $factoryModel['order'])
            ->where('icon', $factoryModel['icon'])
            ->where('module_id', $factoryModel['module_id'])
            ->where('permission_id', $factoryModel['permission_id'])
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
            ->where('order', $this->model->order)
            ->where('icon', $this->model->icon)
            ->where('module_id', $this->model->module_id)
            ->where('permission_id', $this->model->permission_id)
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
    public function se_obtiene_acces_denied_http_exception_cuando_se_crea_un_menu_sin_privilegios()
    {

        $module = Module::factory()->create();
        $permission = Permission::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'module_id' => $module->id,
            'permission_id' => $permission->id,
            'url' => $this->faker->url,
            'icon' => $this->faker->lastName,
            'order' => 1,
            'active' => $this->faker->boolean
        ];

        $this->role->revokePermissionTo('menu.create');

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
        $this->role->revokePermissionTo('menu.update');
        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'name' => 'resource modificado'
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
        $this->role->revokePermissionTo('menu.delete');

        $uri = sprintf('%s/%s', self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);


        $this->assertDatabaseHas($this->table, [
            'name' => $this->model->name,
        ]);;

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

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
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

        $module = Module::factory()->create();
        Menu::factory()->count(20)->for($module)->create();

        $list = Menu::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s&paginate=%s', $this->table, $i, $DEFAULT_PAGINATE))
                ->assertStatus(Response::HTTP_OK);

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'active' => 'boolean',
                            'icon' => 'string',
                            'module' => 'array',
                            'module_id' => 'integer',
                            'permission_id' => 'integer',
                            'permission' => 'array',
                            'order' => 'integer',
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
        $module = Module::factory()->create();
        Menu::factory()->count(20)->for($module)->create();

        $list = Menu::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s', $this->table, $i))
                ->assertStatus(Response::HTTP_OK);

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'active' => 'boolean',
                            'icon' => 'string',
                            'module' => 'array',
                            'module_id' => 'integer',
                            'permission_id' => 'integer',
                            'permission' => 'array',
                            'order' => 'integer',
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
        $uri = sprintf('%s/%s/status', self::BASE_URI, $this->model->id);


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

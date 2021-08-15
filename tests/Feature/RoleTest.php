<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\RolePermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private $user, $permission, $model, $role;
    private string $perPage;
    private string $table;

    const BASE_URI = '/api/v1/roles';

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionsSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $modelClass = new Role();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->model = Role::factory()->create();
        $this->permission = Permission::where('name', 'role.create')->first();
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
    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        $uri = self::BASE_URI;

        $countModels = Role::count();

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
        Role::factory()->count(20)->create();

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

        $this->withoutExceptionHandling();

        $uri = sprintf("%s/%s", self::BASE_URI, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('id', $this->model->id)
            ->where('name', $this->model->name)
            ->where('guard_name', $this->model->guard_name)
            ->where('created_at', $this->model->created_at->format('d/m/Y'))
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
            'name' => 'new role',
            'active' => true,
            'guard_name' => 'api'
        ];

        $uri = self::BASE_URI;

        $response = $this->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', $factoryModel['name'])
            ->where('guard_name', $factoryModel['guard_name'])
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

        $this->assertDatabaseMissing($this->table, ['name' => $this->model->name]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('role.create');

        $factoryModel = [
            'name' => 'new role',
            'active' => true,
            'guard_name' => 'api'
        ];

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
        $this->role->revokePermissionTo('role.update');

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
        $this->role->revokePermissionTo('role.delete');

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

        Role::factory()->count(20)->create();

        $list = Role::count();

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
        Role::factory()->count(20)->create();

        $list = Role::count();

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

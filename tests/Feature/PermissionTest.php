<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionPermissionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    private $user, $model, $role, $table;
    private string $perPage;

    const BASE_URI = '/api/v1/permissions';


    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(PermissionPermissionSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $modelClass = new Permission();

        $user->assignRole($role);

        $this->user = $user;
        $this->role = $role;
        $this->model = Permission::where('name', 'permission.create')->first();
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

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        $this->withoutExceptionHandling();

        $uri = self::BASE_URI;

        $countModels = Permission::count();

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
                        'description' => 'string',
                        'name' => 'string',
                        'model' => 'string',
                        'guard_name' => 'string',
                        'action' => 'string',
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
                        'description' => 'string',
                        'name' => 'string',
                        'model' => 'string',
                        'guard_name' => 'string',
                        'action' => 'string',
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
            ->where('description', $this->model->description)
            ->where('model', $this->model->model)
            ->where('guard_name', $this->model->guard_name)
            ->where('action', $this->model->action)
            ->etc()
        );
    }

    /**
     * @test
     */
    public function test_se_puede_crear_un_recurso(): void
    {
        $factoryModel = [
            'name' => 'fake.create',
            'description' => $this->faker->text,
            'model' => 'Faker',
            'action' => 'create',
            'guard_name' => 'api'
        ];

        $uri = self::BASE_URI;

        $response = $this->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('name', $factoryModel['name'])
            ->where('description', $factoryModel['description'])
            ->where('model', $factoryModel['model'])
            ->where('action', $factoryModel['action'])
            ->where('guard_name', $factoryModel['guard_name'])
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
            ->where('name', 'resource modificado')
            ->where('description',$this->model->description)
            ->where('model', $this->model->model)
            ->where('action', $this->model->action)
            ->where('guard_name', $this->model->guard_name)
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
        $this->role->revokePermissionTo('permission.create');

        $factoryModel = [
            'name' => 'fake.create',
            'description' => $this->faker->text,
            'model' => 'Faker',
            'action' => 'create',
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
        $this->role->revokePermissionTo('permission.update');

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
        $this->role->revokePermissionTo('permission.delete');

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

        $response->assertStatus(Response::HTTP_NOT_FOUND);;

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

        $response->assertJsonFragment([
            'exception' => 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_se_genera_mensages_error_cuando_content_type_no_es_json(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->post('/api/v1/permissions', []);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors'
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @test
     */
    public function se_genera_mensages_error_al_validar_request(): void
    {
        User::factory()->create();

        $response = $this->actingAs($this->user, 'api')->postJson(self::BASE_URI, []);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Permission::factory()->count(20)->create();

        $permissions = Permission::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $permissions % $DEFAULT_PAGINATE;

        $pages = intval(ceil($permissions / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/permissions?page=%s&paginate=%s', $i, $DEFAULT_PAGINATE))
                ->assertStatus(Response::HTTP_OK);

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'description' => 'string',
                            'name' => 'string',
                            'model' => 'string',
                            'guard_name' => 'string',
                            'action' => 'string',
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

        $this->assertDatabaseCount($this->table, $permissions);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Permission::factory()->count(20)->create();

        $list = Permission::count();

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
                            'description' => 'string',
                            'name' => 'string',
                            'model' => 'string',
                            'guard_name' => 'string',
                            'action' => 'string',
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

}

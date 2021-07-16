<?php

namespace Tests\Feature;

use App\Http\Controllers\WorkareaController;
use App\Models\Role;
use App\Models\User;
use App\Models\Workarea;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class WorkareaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private WorkareaController $workareaController;
    private string $perPage;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('workarea.create');
        $role->givePermissionTo('workarea.update');
        $role->givePermissionTo('workarea.delete');
        $role->givePermissionTo('workarea.index');
        $role->givePermissionTo('workarea.show');

        $user->assignRole($role);

        $modelClass = new Workarea();
        $this->workareaController = new WorkareaController();

        $this->user = $user;
        $this->role = $role;
        $this->model = Workarea::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        Workarea::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function(AssertableJson $json) {
            return $json
                ->has('links')
                ->has('meta')
                ->has('data',10 ,function($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'name' => 'string',
                        'active' => 'boolean'
                    ]);
            });
        });
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void //show
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/{$this->table}/{$this->model->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(Workarea::getObjectJsonStructure());

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => $this->model->name,
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {
        $list = Workarea::count();

        $factoryModel = [
            'name' => 'Uroanalisis',
            'active' => true
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' => $response->json()['id'],
            'name' => $factoryModel['name'],
            'active' => $factoryModel['active']
        ]);

        $this->assertDatabaseCount($this->table, ($list + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void // update
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id),  [
                'name' => 'new workarea modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => 'new workarea modificado',
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {
        $list = Workarea::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount($this->table, ($list - 1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $list = Workarea::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'active' => true
        ];

        $this->role->revokePermissionTo('workarea.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('workarea.update');

        $url = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'laboratory name modificado'
            ]);

        $this->assertNotEquals($this->model->name, 'laboratory name modificado');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('workarea.delete');

        $list = Workarea::count();

        $uri = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('/api/v1/%s/%s',$this->table , -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s',$this->table ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%s/%s',$this->table ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('/api/v1/%s/%s',$this->table ,'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Workarea::factory()->count(20)->create();

        $list = Workarea::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for($i = 1; $i <= $pages; $i++){
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s&paginate=%s',$this->table , $i, $DEFAULT_PAGINATE ))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data'])->count());
                }

            }

            $response->assertJsonStructure(Workarea::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Workarea::factory()->count(20)->create();

        $list = Workarea::count();

        $pages = intval(ceil($list / $this->perPage ));
        $mod = $list % $this->perPage ;

        for($i = 1; $i <= $pages; $i++){

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s',$this->table ,$i))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($this->perPage ,  collect($response['data'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($this->perPage ,  collect($response['data'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data'])->count());
                }
            }

            $response->assertJsonStructure(Workarea::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);
    }
}

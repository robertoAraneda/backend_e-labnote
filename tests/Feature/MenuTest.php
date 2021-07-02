<?php

namespace Tests\Feature;

use App\Http\Controllers\MenuController;
use App\Models\Menu;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private MenuController $controller;
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

        $role->givePermissionTo('menu.create');
        $role->givePermissionTo('menu.update');
        $role->givePermissionTo('menu.delete');
        $role->givePermissionTo('menu.index');
        $role->givePermissionTo('menu.show');

        $modelClass = new Menu;
        $this->controller = new MenuController;

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->model = Menu::factory()->for(Module::factory()->create())->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        $module = Module::factory()->create();
        Menu::factory()->count(10)->for($module)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(Menu::getListJsonStructure());
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/{$this->table}/{$this->model->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(Menu::getObjectJsonStructure());

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => $this->model->name,
            'status' => $this->model->status
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void
    {
        $this->withoutExceptionHandling();

        $list = Menu::count();

        $module = Module::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'module_id' => $module->id,
            'status' => $this->faker->numberBetween(0,1)
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);


        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' => $response->json()['id'],
            'name' =>$factoryModel['name'],
            'status' => $factoryModel['status']
        ]);

        $response->assertJsonStructure(Menu::getObjectJsonStructure());

        $this->assertDatabaseCount($this->table, ($list + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void
    {

        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id),  [
                'name' => 'new menu modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => 'new menu modificado',
            'status' => $this->model->status
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void
    {
        $list = Module::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%s/%s', $this->table, $this->role->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount($this->table, ($list -1));

    }

    public function test_no_se_puede_crear_un_registro_sin_privilegios(): void
    {
        $list = Menu::count();

        $module = Module::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'module_id' => $module->id,
            'status' => $this->faker->numberBetween(0,1)
        ];

        $this->role->revokePermissionTo('menu.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertJsonFragment([
            'message' => 'This action is unauthorized.'
        ]);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_obtiene_acces_denied_http_exception_cuando_se_crea_un_menu_sin_privilegios(){

        $module = Module::factory()->create();

        $factoryModel = [
            'name' => $this->faker->name,
            'module_id' => $module->id,
            'status' => $this->faker->numberBetween(0,1)
        ];

        $this->role->revokePermissionTo('menu.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

    }

    public function test_no_se_puede_editar_un_registro_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('menu.update');
        $url = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'laboratory name modificado'
            ]);

        $this->assertNotEquals('laboratory name modificado', $this->model->name);

        $response->assertJsonFragment([
            'message' => 'This action is unauthorized.'
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('menu.update');
        $url = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'laboratory name modificado'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('menu.delete');

        $list = Menu::count();
        $uri = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('/api/v1/%s/%s',$this->table ,-5);
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

    public function test_se_obtiene_error_500_si_parametro_no_es_numerico_al_buscar(): void
    {

        $uri = sprintf('/api/v1/%s/%s',$this->table ,'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        $module = Module::factory()->create();
        Menu::factory()->count(20)->for($module)->create();

        $list = Menu::count();

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

            $response->assertJsonStructure(Menu::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        $module = Module::factory()->create();
        Menu::factory()->count(20)->for($module)->create();

        $list = Menu::count();

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

            $response->assertJsonStructure(Menu::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_expect_exception_cuando_el_parametro_id_no_es_integer_find_by_id(): void
    {
        $this->expectException(\TypeError::class);

        $this->controller->findById('string');
    }
}
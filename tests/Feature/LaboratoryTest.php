<?php

namespace Tests\Feature;

use App\Http\Controllers\LaboratoryController;
use App\Models\Laboratory;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LaboratoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private LaboratoryController $laboratoryController;
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

        $role->givePermissionTo('laboratory.create');
        $role->givePermissionTo('laboratory.update');
        $role->givePermissionTo('laboratory.delete');
        $role->givePermissionTo('laboratory.index');
        $role->givePermissionTo('laboratory.show');

        $modelClass = new Laboratory();
        $this->laboratoryController = new LaboratoryController();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->model = Laboratory::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        Laboratory::factory()->count(10)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(Laboratory::getListJsonStructure());
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/{$this->table}/{$this->model->id}" );


        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(Laboratory::getObjectJsonStructure());

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => $this->model->name,
            'address' => $this->model->address,
            'email' => $this->model->email,
            'phone' =>$this->model->phone,
            'redirect' => $this->model->redirect,
            'status' => $this->model->status
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void
    {
       // $this->withoutExceptionHandling();
        $list = Laboratory::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'redirect' => "http://".$this->faker->languageCode.".elabnote.cl",
            'status' => 1
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' => $response->json()['id'],
            'name' =>$factoryModel['name'],
            'address' => $factoryModel['address'],
            'email' => $factoryModel['email'],
            'phone' => $factoryModel['phone'],
            'redirect' => $factoryModel['redirect'],
            'status' => $factoryModel['status']
        ]);

        $response->assertJsonStructure(Laboratory::getObjectJsonStructure());

        $this->assertDatabaseCount($this->table, ($list + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id),  [
                'name' => 'new laboratory modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'id' => $response->json()['id'],
            'name' => 'new laboratory modificado',
            'address' => $this->model->address,
            'email' => $this->model->email,
            'phone' =>$this->model->phone,
            'redirect' => $this->model->redirect,
            'status' => $this->model->status
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void
    {
        $list = Laboratory::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount($this->table, ($list -1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $list = Laboratory::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'status' => 1
        ];

        $this->role->revokePermissionTo('laboratory.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list );

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
       $this->role->revokePermissionTo('laboratory.update');
        $url = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'laboratory name modificado'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('laboratory.delete');

        $list = Laboratory::count();
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

    public function test_se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('/api/v1/%s/%s',$this->table ,'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Laboratory::factory()->count(20)->create();

        $list = Laboratory::count();

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

            $response->assertJsonStructure(Laboratory::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Laboratory::factory()->count(20)->create();

        $list = Laboratory::count();

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

            $response->assertJsonStructure(Laboratory::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_obtiene_error_not_found_cuando_no_existe_id()
    {
        $response = $this->laboratoryController->findById('string');

        $statusCode = $response->getStatusCode();

        $this->assertEquals(400, $statusCode);
    }

}

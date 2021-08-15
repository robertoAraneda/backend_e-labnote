<?php

namespace Tests\Feature;

use App\Http\Controllers\LoincController;
use App\Models\Loinc;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LoincPermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoincTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private LoincController $loincController;
    private string $perPage;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(LoincPermissionSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $user->assignRole($role);

        $modelClass = new Loinc();
        $this->loincController = new LoincController();

        $this->user = $user;
        $this->role = $role;
        $this->model = Loinc::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }


    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {

        $this->withoutExceptionHandling();

        Loinc::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%ss', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $countModels = Loinc::count();

        $response->assertJson(function(AssertableJson $json) use ( $countModels ){
            return $json
                ->has('_links')
                ->has('count')
                ->has('collection', $countModels ,function($json) {
                    $json->whereAllType([
                        'loinc_num' => 'string',
                        'long_common_name' => 'string',
                        '_links' => 'array'
                    ]);
                });
        });
    }


    public function test_se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        Loinc::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%ss?page=1', $this->table));

        $response->assertStatus(Response::HTTP_OK);

        $page =  $this->perPage;

        $response->assertJson(function(AssertableJson $json) use ( $page ){
            return $json
                ->has('links')
                ->has('meta')
                ->has('data.collection', $page ,function($json) {
                    $json->whereAllType([
                        'loinc_num' => 'string',
                        '_links' => 'array'
                    ]);
                });
        });
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void //show
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/{$this->table}s/{$this->model->loinc_num}" );

        $response->assertStatus(Response::HTTP_OK);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('loinc_num', $this->model->loinc_num)
                ->etc()
            );
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {

        $factoryModel = [
            'loinc_num' => $this->faker->slug,
            'long_common_name' => $this->faker->title
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}s",  $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('loinc_num', $factoryModel['loinc_num'])
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'loinc_num' => $factoryModel['loinc_num'],
        ]);
    }

    public function test_se_puede_modificar_un_recurso(): void // update
    {

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/%ss/%s', $this->table, $this->model->loinc_num),  [
                'long_common_name' => 'long_common_name modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('long_common_name', 'long_common_name modificado')
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'long_common_name' => 'long_common_name modificado'
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%ss/%s', $this->table, $this->model->loinc_num));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing($this->table, [
            'long_common_name' => $this->model->long_common_name,
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {

        $factoryModel = [
            'loinc_num' => $this->faker->slug,
            'long_common_name' => $this->faker->title,
        ];

        $this->role->revokePermissionTo('loinc.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}s",  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'loinc_num' => $factoryModel['loinc_num'],
        ]);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('loinc.update');

        $url = sprintf('/api/v1/%ss/%s',$this->table ,$this->model->loinc_num);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'long_common_name' => 'resource modificado'
            ]);

        $this->assertDatabaseMissing($this->table, [
            'long_common_name' => 'resource modificado'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('loinc.delete');

        $uri = sprintf('/api/v1/%ss/%s',$this->table ,$this->model->loinc_num);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'long_common_name' => $this->model->long_common_name,
        ]);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $uri = sprintf('/api/v1/%ss/%s',$this->table , -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%ss/%s',$this->table ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }


    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('/api/v1/%ss/%s',$this->table ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Loinc::factory()->count(20)->create();

        $list = Loinc::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for($i = 1; $i <= $pages; $i++){
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%ss?page=%s&paginate=%s',$this->table , $i, $DEFAULT_PAGINATE ))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data']['collection'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data']['collection'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data']['collection'])->count());
                }

            }
            $response->assertJson(function(AssertableJson $json){
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0',function($json) {
                        $json->whereAllType([
                            'loinc_num' => 'string',
                            'long_common_name' => 'string',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Loinc::factory()->count(20)->create();

        $list = Loinc::count();

        $pages = intval(ceil($list / $this->perPage ));
        $mod = $list % $this->perPage ;

        for($i = 1; $i <= $pages; $i++){

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%ss?page=%s',$this->table ,$i))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($this->perPage ,  collect($response['data']['collection'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($this->perPage ,  collect($response['data']['collection'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data']['collection'])->count());
                }
            }

            $response->assertJson(function(AssertableJson $json){
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0',function($json) {
                        $json->whereAllType([
                            'loinc_num' => 'string',
                            'long_common_name' => 'string',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }

}

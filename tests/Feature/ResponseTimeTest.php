<?php

namespace Tests\Feature;

use App\Http\Controllers\ResponseTimeController;
use App\Models\ResponseTime;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ResponseTimePermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ResponseTimeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private ResponseTimeController $responseTimeController;
    private string $table;
    private string $base_url;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(ResponseTimePermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('responseTime.create');
        $role->givePermissionTo('responseTime.update');
        $role->givePermissionTo('responseTime.delete');
        $role->givePermissionTo('responseTime.index');
        $role->givePermissionTo('responseTime.show');

        $user->assignRole($role);

        $modelClass = new ResponseTime();
        $this->responseTimeController = new ResponseTimeController();

        $this->user = $user;
        $this->role = $role;
        $this->model = ResponseTime::factory()->create();
        $this->table = $modelClass->getTable();
        $this->base_url = '/api/v1/response-times';

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        ResponseTime::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson($this->base_url);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function(AssertableJson $json) {
            return $json
                ->has('0',function($json) {
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
            ->getJson(sprintf('%s/%s', $this->base_url, $this->model->id));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(ResponseTime::getObjectJsonStructure());

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => $this->model->name,
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {
        $list = ResponseTime::count();

        $factoryModel = [
            'name' => 'Tiempo de respuesta 1',
            'active' => true
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson($this->base_url,  $factoryModel);

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
            ->putJson(sprintf('%s/%s', $this->base_url, $this->model->id),  [
                'name' => 'new responseTime modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => 'new responseTime modificado',
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('%s/%s', $this->base_url, $this->model->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id'=> $this->model->id]);
        $this->assertSoftDeleted($this->model);
    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $list = ResponseTime::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'active' => true
        ];

        $this->role->revokePermissionTo('responseTime.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson($this->base_url,  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('responseTime.update');

        $url = sprintf('%s/%s',$this->base_url ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'responseTime name modificado'
            ]);

        $this->assertNotEquals($this->model->name, 'responseTime name modificado');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('responseTime.delete');

        $list = ResponseTime::count();

        $uri = sprintf('%s/%s',$this->base_url ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url , -5);
        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,'string');

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson($uri);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }


}

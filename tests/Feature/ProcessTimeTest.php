<?php

namespace Tests\Feature;

use App\Http\Controllers\ProcessTimeController;
use App\Models\ProcessTime;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ProcessTimePermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProcessTimeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private ProcessTimeController $processTimeController;
    private string $perPage;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(ProcessTimePermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('processTime.create');
        $role->givePermissionTo('processTime.update');
        $role->givePermissionTo('processTime.delete');
        $role->givePermissionTo('processTime.index');
        $role->givePermissionTo('processTime.show');

        $user->assignRole($role);

        $modelClass = new ProcessTime();
        $this->processTimeController = new ProcessTimeController();

        $this->user = $user;
        $this->role = $role;
        $this->model = ProcessTime::factory()->create();
        $this->table = $modelClass->getTable();

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        ProcessTime::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/%s', $this->table));

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
            ->getJson("/api/v1/{$this->table}/{$this->model->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(ProcessTime::getObjectJsonStructure());

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => $this->model->name,
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void //store
    {
        $list = ProcessTime::count();

        $factoryModel = [
            'name' => 'Tiempo de proceso 1',
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
                'name' => 'new processTime modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' => $this->model->id,
            'name' => 'new processTime modificado',
            'active' => $this->model->active
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void //destroy
    {
        $this->withoutExceptionHandling();

        $list = ProcessTime::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/%s/%s', $this->table, $this->model->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount($this->table, ($list - 1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $list = ProcessTime::count();

        $factoryModel = [
            'name' => $this->faker->name,
            'active' => true
        ];

        $this->role->revokePermissionTo('processTime.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/{$this->table}",  $factoryModel);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('processTime.update');

        $url = sprintf('/api/v1/%s/%s',$this->table ,$this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->putJson($url,  [
                'name' => 'processTime name modificado'
            ]);

        $this->assertNotEquals($this->model->name, 'processTime name modificado');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('processTime.delete');

        $list = ProcessTime::count();

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

}

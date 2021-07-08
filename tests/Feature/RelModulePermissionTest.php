<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RelModulePermissionPermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RelModulePermissionTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(RelModulePermissionPermissionsSeeder::class);


        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('modulePermission.create');
        $role->givePermissionTo('modulePermission.index');

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->model = Module::factory()
            ->hasAttached(Permission::factory()->count(5), ['user_id' => $user->id])
            ->create();
    }

    public function test_se_puede_obtener_una_lista_de_menus_de_un_modulo(): void
    {

        $url = "/api/v1/modules/{$this->model->id}/permissions";


        $response = $this->actingAs($this->user, 'api')
            ->getJson($url)
            ->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('0', fn ($json) =>
        $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'model' => 'string',
            'action' => 'string',
            'description' => 'string'
        ]))
        );
    }

    public function test_se_puede_obtener_los_modulos_asociados_a_un_laboratorio_del_total_de_modulos(): void
    {

        $this->withoutExceptionHandling();
        $url = "/api/v1/modules/{$this->model->id}/permissions?cross=true&role_id={$this->role->id}";

        $response = $this->actingAs($this->user, 'api')
            ->getJson($url);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('0', fn ($json) =>
        $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'model' => 'string',
            'action' => 'string',
            'description' => 'string',
            'checkbox' => 'boolean'
        ]))
        );

    }

    public function test_se_puede_crear_un_recurso(): void
    {

        $this->withoutExceptionHandling();
        $modules = Module::factory()->create();
        $permissions = Permission::factory()->count(6)->create()->pluck('id');

        $stored = $permissions->splice(3);

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/modules/{$modules->id}/permissions",
                $stored->all());

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('0', fn ($json) =>
        $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'model' => 'string',
            'action' => 'string',
            'description' => 'string'
        ]))
        );
    }
}

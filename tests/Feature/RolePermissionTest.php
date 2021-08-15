<?php

namespace Tests\Feature;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user;
    private RoleController $controller;
    private string $perPage;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionsSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('role.create');
        $role->givePermissionTo('role.index');
        $role->givePermissionTo('role.show');

        $this->controller = new RoleController();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_del_recurso(): void
    {

        $this->withoutExceptionHandling();


        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/roles/%s/permissions', $this->role->id));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn (AssertableJson $json) =>
        $json->whereType('0.id', 'integer')
            ->whereAllType([
                '0.name' => 'string',
                '0.description'=> 'string|null',
                '0.model'=> 'string|null',
                '0.action'=> 'string|null'
            ])
        );

    }

    /**
     * @test
     */
    public function test_se_puede_crear_un_recurso(): void
    {
        $role = Role::where('name', 'Secretaria')->first();
        $permissionCreate = Permission::where('name', 'role.create')->first();
        $permissionUpdate = Permission::where('name', 'role.index')->first();
        $permissionDelete = Permission::where('name', 'role.show')->first();

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/roles/{$role->id}/permissions",
                [
                    $permissionCreate->id,
                    $permissionUpdate->id,
                    $permissionDelete->id,
                ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'model' => 'string|null',
            'action' => 'string|null',
            'description' => 'string|null',
            'guard_name' => 'string',
            '_links' => 'array',
        ]))
        );

    }
}

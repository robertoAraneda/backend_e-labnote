<?php

namespace Tests\Feature;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
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

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('rolePermission.create');
        $role->givePermissionTo('rolePermission.update');
        $role->givePermissionTo('rolePermission.delete');
        $role->givePermissionTo('rolePermission.index');
        $role->givePermissionTo('rolePermission.show');

        $this->controller = new RoleController();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
       // $this->model = RolePermission::factory()->create();
        //$this->perPage = $modelClass->getPerPage();
        //$this->table = $modelClass->getTable();

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/roles/%s/permissions', $this->role->id));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function(AssertableJson $json) {
           return $json->has('0', function($json) {
               $json->whereAllType([
                   'id' => 'integer',
                   'name' => 'string',
                   'permissions' => 'array',
                   'guard_name' => 'string',
                   'created_at' => 'string',
                   'updated_at' => 'string'
               ]);
           });
        });
    }

    public function test_se_puede_crear_un_recurso(): void
    {
        $role = Role::where('name', 'Secretaria')->first();
        $permissionCreate = Permission::where('name', 'rolePermission.create')->first();
        $permissionUpdate = Permission::where('name', 'rolePermission.update')->first();
        $permissionDelete = Permission::where('name', 'rolePermission.delete')->first();

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/roles/{$role->id}/permissions",
                [
                    $permissionCreate->id,
                    $permissionUpdate->id,
                    $permissionDelete->id,
                ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'guard_name' => 'string',
                'created_at' => 'string',
                'updated_at' => 'string',
                'permissions' => 'array'
            ])
        );

    }



}

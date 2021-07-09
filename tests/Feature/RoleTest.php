<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\RolePermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private $user, $permission, $role;
    private string $perPage;
    private string $table;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(RolePermissionsSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();
        $permission = Permission::where('name', 'role.create')->first();

        $role->givePermissionTo('role.create');
        $role->givePermissionTo('role.update');
        $role->givePermissionTo('role.delete');
        $role->givePermissionTo('role.index');
        $role->givePermissionTo('role.show');

        $modelClass = new Role();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->permission = $permission;
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/roles');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->whereType('0.id', 'integer')
            ->whereAllType([
                '0.name' => 'string',
                '0.guardName' => 'string',
                '0.createdAt' => 'string',
            ])
        );
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/roles/{$this->role->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' =>  $this->role->id,
            'name' => $this->role->name,
            'createdUser' => $this->role->created_user->names,
            'createdAt' => $this->role->created_at->format('d/m/Y'),
            'guardName' => $this->role->guard_name,
            'active' => (bool) $this->role->active
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void
    {

        $roles = Role::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/roles',  [
                'name' => 'new role',
                'active' => true,
                'created_user_id' => $this->user->id,
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => 'new role',
            'active' => true,
            'createdUser' => $this->user->names,
            'createdAt' => $response->json()['createdAt'],
            'guardName' => 'api'
        ]);

        $this->assertDatabaseCount('roles', ($roles + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/roles/%s', $this->role->id),  [
                'name' => 'new role modificado',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'id' =>  $this->role->id,
            'name' => 'new role modificado',
            'createdUser' => $this->role->created_user->names,
            'createdAt' => $this->role->created_at->format('d/m/Y'),
            'guardName' => $this->role->guard_name,
            'active' => (bool) $this->role->active
        ]);
    }

    public function test_se_puede_eliminar_un_recurso(): void
    {
        $roles = Role::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/roles/%s', $this->role->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('roles', ($roles -1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('role.create');

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/roles',  [
                'name' => 'new role',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('role.update');

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/roles/%s', $this->role->id),  [
                'name' => 'user-create-modificado',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('role.delete');

        $roles = Role::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/roles/%s', $this->role->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount('roles', $roles);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/roles/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/role/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/roles/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

}

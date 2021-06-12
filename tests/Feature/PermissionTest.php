<?php

namespace Tests\Feature;

use App\Http\Resources\PermissionResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionTest extends TestCase
{

   use RefreshDatabase;

   private $user;
   private $permission;
   private $role;

    public function setUp():void {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $permission = Permission::factory()->create();
        $role = Role::factory()->create();

        $role->givePermissionTo('permission.create');

        $user->assignRole($role);

        $this->user =  $user;
        $this->permission =  $permission;
        $this->role = $role;

    }

    public function test_se_puede_obtener_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/permissions/{$this->permission->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('permissions', 1);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => $this->permission->name,
        ]);
    }

    public function test_se_puede_obtener_un_permiso_formateado(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/permissions/{$this->permission->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('permissions', 1);
    }

    public function test_se_puede_crear_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/permissions',  [
                'name' => 'user-create',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('permissions', 2);

    }

    public function test_se_puede_crear_un_permiso_formateado(): void
    {
       // $user = User::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/permissions',  [
                'name' => 'user-create',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('permissions', 2);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => 'user-create',
        ]);

    }

    public function test_se_puede_modificar_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/permissions/%s', $this->permission->id),  [
                'name' => 'user-create-modificado',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'id' =>  $this->permission->id,
            'name' => 'user-create-modificado',
        ]);

        $this->assertDatabaseCount('permissions', 1);

    }

    public function test_se_puede_eliminar_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/permissions/%s', $this->permission->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('permissions', 0);

    }

    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_eliminar_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('users', 1);
    }
    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_editar_un_permiso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('users', 1);
    }
    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_mostrar_un_permiso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('users', 1);
    }


}

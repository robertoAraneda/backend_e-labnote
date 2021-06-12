<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

    }

    public function test_se_puede_crear_un_rol(): void
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/roles',  [
                'name' => 'super-user',
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('roles', 1);

    }

    public function test_se_puede_crear_un_rol_formateado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/roles',  [
                'name' => 'super-user'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('roles', 1);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => 'super-user'
        ]);

    }

    public function test_se_puede_modificar_un_rol(): void
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson(sprintf('/api/v1/roles/%s', $role->id),  [
                'name' => 'super-user-modificado'
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'id' =>  $role->id,
            'name' => 'super-user-modificado',
        ]);

        $this->assertDatabaseCount('roles', 1);

    }

    public function test_se_puede_eliminar_un_rol(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson(sprintf('/api/v1/roles/%s', $role->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('roles', 0);

    }

    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_eliminar_un_rol(): void
    {
        $user = User::factory()->create();
        Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson(sprintf('/api/v1/roles/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('roles', 1);
    }
    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_editar_un_rol(): void
    {
        $user = User::factory()->create();
        Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson(sprintf('/api/v1/roles/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('roles', 1);
    }
    public function test_se_obtiene_un_not_found_si_parametro_es_menor_a_cero_al_mostrar_un_rol(): void
    {
        $user = User::factory()->create();
        Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson(sprintf('/api/v1/roles/%s', -5));

        $response->assertNotFound();

        $this->assertDatabaseCount('roles', 1);
    }

}

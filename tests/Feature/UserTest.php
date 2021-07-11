<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\UserPermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $permission;
    private $role;

    public function setUp():void {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(UserPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();
        $permission = Permission::where('name', 'user.create')->first();

        $role->givePermissionTo('user.create');
        $role->givePermissionTo('user.update');
        $role->givePermissionTo('user.delete');
        $role->givePermissionTo('user.index');
        $role->givePermissionTo('user.show');

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->permission = $permission;

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {

       $users =  User::factory()->count(10)->create();

       dd($users);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/users');

        $response->assertStatus(Response::HTTP_OK);

    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/users/{$this->user->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(['id', 'rut', 'names', 'lastname', 'mother_lastname', 'email']);
        $response->assertExactJson(
            [
                'id' => $this->user->id,
                'rut' => $this->user->rut,
                'names' => $this->user->names,
                'lastname' => $this->user->lastname,
                'mother_lastname' => $this->user->mother_lastname,
                'email' => $this->user->email
            ]
        );
    }

    public function test_se_puede_crear_un_recurso(): void
    {

       $users = User::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/users',  [
                'rut' => '15.654.738-7',
                'names' => $this->faker->name(),
                'lastname' => $this->faker->firstName(),
                'mother_lastname' => $this->faker->firstName(),
                'email' => $this->faker->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // password
                'remember_token' => Str::random(10),
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJsonStructure(['id', 'rut', 'names', 'lastname', 'mother_lastname', 'email']);

        $this->assertDatabaseCount('users', ($users + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/users/%s', $this->user->id),  [
                'names' => 'new names modificado',
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson([
            'id' =>  $this->user->id,
            'rut' => $this->user->rut,
            'names' => 'new names modificado',
            'lastname' => $this->user->lastname,
            'mother_lastname' => $this->user->mother_lastname,
            'email' => $this->user->email
        ]);
        $response->assertJsonStructure(['id', 'rut', 'names', 'lastname', 'mother_lastname', 'email']);
    }

    public function test_se_puede_eliminar_un_recurso(): void
    {
        $users = User::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/users/%s', $this->user->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('users', ($users -1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('user.create');

        $users = User::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/users',  [
                'rut' => '15.654.738-7',
                'names' => $this->faker->name(),
                'lastname' => $this->faker->firstName(),
                'mother_lastname' => $this->faker->firstName(),
                'email' => $this->faker->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // password
                'remember_token' => Str::random(10),
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount('users', $users);
    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('user.update');

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/users/%s', $this->user->id),  [
                'rut' => '15.654.738-7',
                'names' => 'nombre modificado',
                'lastname' => $this->faker->firstName(),
                'mother_lastname' => $this->faker->firstName(),
                'email' => $this->faker->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // password
                'remember_token' => Str::random(10),
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('user.delete');

        $users = User::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/users/%s', $this->user->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount('users', $users);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/users/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/users/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/users/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

}

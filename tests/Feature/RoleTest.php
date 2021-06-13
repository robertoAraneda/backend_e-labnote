<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private $user, $permission, $role, $table, $perPage;

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(PermissionSeeder::class);
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
        $response->assertJsonStructure([
            'data' => [['id', 'name']],
            'links',
            'meta',
        ]);
    }

    public function test_se_puede_obtener_el_detalle_del_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/roles/{$this->role->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => $this->role->name,
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void
    {

        $roles = Role::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/roles',  [
                'name' => 'new role',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => 'new role',
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

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Role::factory()->count(20)->create();

        $list = Role::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for($i = 1; $i <= $pages; $i++){
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s&paginate=%s',$this->table , $i, $DEFAULT_PAGINATE ))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($DEFAULT_PAGINATE ,  collect($response['data'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data'])->count());
                }

            }

            $response->assertJsonStructure(Role::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Role::factory()->count(20)->create();

        $list = Role::count();

        $pages = intval(ceil($list / $this->perPage ));
        $mod = $list % $this->perPage ;

        for($i = 1; $i <= $pages; $i++){

            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/%s?page=%s',$this->table ,$i))
                ->assertStatus(Response::HTTP_OK);

            if($i < $pages){
                $this->assertEquals($this->perPage ,  collect($response['data'])->count());
            }else{
                if($mod == 0){
                    $this->assertEquals($this->perPage ,  collect($response['data'])->count());
                }else{
                    $this->assertEquals($mod ,  collect($response['data'])->count());
                }
            }

            $response->assertJsonStructure(Role::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

}

<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionTest extends TestCase
{

   use RefreshDatabase;

   private $user, $permission, $role, $table, $perPage;


    public function setUp():void {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();
        $permission = Permission::where('name', 'permission.create')->first();

        $role->givePermissionTo('permission.create');
        $role->givePermissionTo('permission.update');
        $role->givePermissionTo('permission.delete');
        $role->givePermissionTo('permission.index');
        $role->givePermissionTo('permission.show');

        $modelClass = new Permission();

        $user->assignRole($role);

        $this->user =  $user;
        $this->role = $role;
        $this->permission = $permission;
        $this->perPage = $modelClass->getPerPage();
        $this->table = $modelClass->getTable();

    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/permissions');

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
            ->getJson("/api/v1/permissions/{$this->permission->id}" );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => $this->permission->name,
        ]);
    }

    public function test_se_puede_crear_un_recurso(): void
    {
        $permissions = Permission::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/permissions',  [
                'name' => 'user-create',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertExactJson([
            'id' =>  $response->json()['id'],
            'name' => 'user-create',
        ]);

        $this->assertDatabaseCount('permissions', ($permissions + 1));

    }

    public function test_se_puede_modificar_un_recurso(): void
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
    }

    public function test_se_puede_eliminar_un_recurso(): void
    {
        $permissions = Permission::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/permissions/%s', $this->permission->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('permissions', ($permissions -1));

    }

    public function test_se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('permission.create');

        $permissions = Permission::count();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/permissions',  [
                'name' => 'user-create',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount('permissions', $permissions);
    }

    public function test_se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('permission.update');

        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/permissions/%s', $this->permission->id),  [
                'name' => 'user-create-modificado',
                'guard_name' => 'api'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('permission.delete');

        $permissions = Permission::count();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/permissions/%s', $this->permission->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseCount('permissions', $permissions);

    }

    public function test_se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->getJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->putJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(sprintf('/api/v1/permissions/%s', -5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_se_genera_mensages_error_cuando_content_type_no_es_json(): void
    {

        $response = $this->actingAs($this->user, 'api')
            ->post('/api/v1/permissions',[]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors'
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function test_se_genera_mensages_error_al_validar_request(): void
    {
        User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login',[]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {

        Permission::factory()->count(20)->create();

        $permissions = Permission::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $permissions % $DEFAULT_PAGINATE;

        $pages = intval(ceil($permissions / $DEFAULT_PAGINATE));

        for($i = 1; $i <= $pages; $i++){
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('/api/v1/permissions?page=%s&paginate=%s', $i, $DEFAULT_PAGINATE ))
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

            $response->assertJsonStructure([
                'data' => [['id', 'name']],
                'links',
                'meta',
            ]);
        }

        $this->assertDatabaseCount($this->table, $permissions );

    }

    public function test_se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Permission::factory()->count(20)->create();

        $list = Permission::count();

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

            $response->assertJsonStructure(Permission::getListJsonStructure());
        }

        $this->assertDatabaseCount($this->table, $list);

    }

}

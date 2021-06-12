<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
   use RefreshDatabase;

    public function setUp():void {
        parent::setUp();
        $this->artisan('passport:install');
        $this->seed(PermissionSeeder::class);

    }

    public function test_se_puede_obtener_un_usuario_formateado(): void
    {

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/v1/users/{$user->id}" );

        $userFounded = $response->json();

        $response->assertStatus(200);
        $response->assertExactJson( [
            'id' => $userFounded['id'],
            'rut' =>  $userFounded['rut'],
            'names' => $userFounded['names'],
            'lastname' => $userFounded['lastname'],
            'mother_lastname' => $userFounded['mother_lastname'],
            'email' => $userFounded['email']
        ]);
    }

    public function test_se_puede_obtener_un_usuario_con_un_administrador_autenticado(): void
    {


        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/users',  [
                'rut' => '15.654.738-7',
                'names' => 'Roberto Alejandro',
                'lastname' => 'Araneda',
                'mother_lastname' => 'Espinoza',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345')
            ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseCount('users',2);
    }

    public function test_se_puede_obtener_un_usuario_formateado_al_crear_un_usuario(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/users',  [
                'rut' => '15.654.738-7',
                'names' => 'Roberto Alejandro',
                'lastname' => 'Araneda',
                'mother_lastname' => 'Espinoza',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345')
            ]);
        $userFounded =  $response->json();
        $response->assertExactJson( [
            'id' => $userFounded['id'],
            'rut' =>  $userFounded['rut'],
            'names' => $userFounded['names'],
            'lastname' => $userFounded['lastname'],
            'mother_lastname' => $userFounded['mother_lastname'],
            'email' => $userFounded['email']
        ]);
    }

    public function test_se_puede_obtener_una_lista_de_usuarios(): void
    {

        $user = User::factory()->create();

        User::factory()->count(4)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users')
            ->assertStatus(Response::HTTP_OK);

        $data = $response->json();

        $this->assertEquals(5,  collect($data['data'])->count());
    }

    public function test_se_puede_obtener_una_lista_de_formateada_de_usuarios(): void
    {

        $user = User::factory()->create();

        User::factory()->count(4)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users')
            ->assertStatus(Response::HTTP_OK);

        $data = $response->json();

        $this->assertEquals(User::all()->map(function($item){
            return [
                'id' => $item['id'],
                'rut' =>  $item['rut'],
                'names' => $item['names'],
                'lastname' => $item['lastname'],
                'mother_lastname' => $item['mother_lastname'],
                'email' => $item['email']
            ];
        }),  collect($data['data']));
    }

    public function test_se_puede_obtener_una_lista_paginada_de_usuarios(): void
    {

        $user = User::factory()->create();

        User::factory()->count(4)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users')
            ->assertStatus(Response::HTTP_OK);

        $response->assertJson(function(AssertableJson $json){
            $json->whereAllType([
                    'data' => 'array',
                    'links' => 'array',
                    'meta' => 'array',
            ]);
        });
    }

    public function test_se_puede_obtener_una_lista_paginada_cuando_se_modifica_la_pagina(): void
    {
        $user = User::factory()->create();

        User::factory()->count(8)->create();

        $responsePageOne = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users?page=1')
            ->assertStatus(Response::HTTP_OK);

        $dataPageOne = $responsePageOne->json();

        $this->assertEquals(5,  collect($dataPageOne['data'])->count());

        $responsePageTwo = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users?page=2')
            ->assertStatus(Response::HTTP_OK);

        $dataPageTwo = $responsePageTwo->json();

        $this->assertEquals(4,  collect($dataPageTwo['data'])->count());

    }

    public function test_se_puede_obtener_una_lista_paginada_cuando_se_modifica_el_limite(): void
    {

        $user = User::factory()->create();

        User::factory()->count(8)->create();

        $this->assertDatabaseCount('users', 9);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users?page=1&paginate=10')
            ->assertStatus(Response::HTTP_OK);

        $data= $response->json();

        $this->assertEquals(9,  collect($data['data'])->count());

    }

    public function test_se_puede_obtener_una_lista_paginada_con_el_limite_por_defecto():void
    {

        $user = User::factory()->create();

        User::factory()->count(8)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/users?page=1')
            ->assertStatus(Response::HTTP_OK);

        $data= $response->json();

        $this->assertEquals(5,  collect($data['data'])->count());
    }

    public function test_se_puede_modificar_un_recurso_usuario():void
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson("/api/v1/users/{$user->id}" ,[
                'id' => $user->id,
                'rut' =>  $user->rut,
                'names' => 'Roberto Alejandro',
                'lastname' => $user->lastname,
                'mother_lastname' => $user->mother_lastname,
                'email' => 'admin@gmail.com'
            ]);

        $userFounded = $response->json();

        $response->assertStatus(200);
        $response->assertExactJson( [
            'id' => $user->id,
            'rut' =>  $user->rut,
            'names' => $userFounded['names'],
            'lastname' => $user->lastname,
            'mother_lastname' => $user->mother_lastname,
            'email' => $userFounded['email']
        ]);
    }

    public function test_se_puede_eliminar_un_usuario(): void
    {
        $this->withoutExceptionHandling();
        $user           = User::factory()->create();
        $userDestroy    = User::factory()->create();

        $this->assertDatabaseCount('users', 2);

        $this->actingAs($user, 'api')
            ->deleteJson("/api/v1/users/{$userDestroy->id}");

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', ['id' => $userDestroy->id]);
    }
}

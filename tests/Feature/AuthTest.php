<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void {
        parent::setUp();
        $this->artisan('passport:install');

    }

    public function test_se_genera_token_authenticacion(): void
    {

        $this->withoutExceptionHandling();

        User::factory()->create();


        $response = $this->postJson('/api/v1/auth/login',[
            'rut' => '15.654.738-7',
            'password' => 'password',
            'remember_me' => true
        ]);
       $response->assertStatus(200);
    }

    public function test_se_crea_un_usuario_sin_autenticar(): void {

        $response = $this->postJson('/api/v1/auth/signup',  [
            'rut' => '15.654.738-7',
            'names' => 'Roberto Alejandro',
            'lastname' => 'Araneda',
            'mother_lastname' => 'Espinoza',
            'email' => 'robaraneda@gmail.com',
            'password' => bcrypt('12345')
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseCount('users',1);
    }


}

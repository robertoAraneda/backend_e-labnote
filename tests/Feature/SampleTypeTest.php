<?php

namespace Tests\Feature;

use App\Http\Controllers\SampleTypeController;
use App\Models\Role;
use App\Models\SampleType;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SampleTypePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SampleTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private SampleTypeController $sampleTypeController;
    private string $perPage;
    private string $table;

    public function setUp(): void
    {

        parent::setUp();

        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(SampleTypePermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('sampleType.create');
        $role->givePermissionTo('sampleType.update');
        $role->givePermissionTo('sampleType.delete');
        $role->givePermissionTo('sampleType.index');
        $role->givePermissionTo('sampleType.show');

        $user->assignRole($role);

        $modelClass = new SampleType();
        $this->sampleTypeController = new SampleTypeController();

        $this->user = $user;
        $this->role = $role;
        $this->model = SampleType::factory()->create();
        $this->perPage = $modelClass->getPerPage();
        $this->table = 'sample-types';

    }

    public function test_se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    public function test_se_puede_obtener_una_lista_del_recurso(): void
    {

        SampleType::factory()->count(20)->create();

        $uri = sprintf('/api/v1/%s', $this->table);
        $countModels = SampleType::count();

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) use ($countModels) {
                return $json
                    ->has('_links')
                    ->has('count')
                    ->has('collection', $countModels, function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'name' => 'string',
                            'active' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });


    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\LaboratoryController;
use App\Models\Laboratory;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaboratoryPermissionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LaboratoryModuleTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * @var
     */
    private $role;
    private $user, $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();

        $this->seed(LaboratoryPermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $role->givePermissionTo('laboratory.create');
        $role->givePermissionTo('laboratory.index');
        $role->givePermissionTo('laboratory.show');

        $user->assignRole($role);

        $this->user = $user;
        $this->role = $role;
        $this->model = Laboratory::factory()
            ->hasAttached(Module::factory()->count(3), ['user_id' => $user->id])
            ->create();
    }

    public function test_se_puede_obtener_una_lista_de_modulos_de_un_laboratorio(): void
    {

        $url = "/api/v1/laboratories/{$this->model->id}/modules";

        $response = $this->actingAs($this->user, 'api')
            ->getJson($url);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'url' => 'string',
            'slug' => 'string',
            'icon' => 'string',
            'active' => 'boolean',
            '_links' => 'array'
        ]))
        );
    }

    public function test_se_puede_obtener_los_modulos_asociados_a_un_laboratorio_del_total_de_modulos(): void
    {
        Module::factory()->count(5)->create();

        Laboratory::factory()
            ->hasAttached(Module::factory()->count(5), ['user_id' => $this->user->id])
            ->create();

        $url = "/api/v1/laboratories/{$this->model->id}/modules?cross=true";

        $response = $this->actingAs($this->user, 'api')
            ->getJson($url);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'active' => 'boolean',
            'icon' => 'string',
            'url' => 'string',
            'slug' => 'string',
            '_links' => 'array',
            'checkbox' => 'boolean'
        ]))
        );

    }

    public function test_se_puede_crear_un_recurso(): void
    {
        $laboratory = Laboratory::factory()->create();
        $modules = Module::factory()->count(6)->create()->pluck('id');

        $modulesStored = $modules->splice(3);

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/v1/laboratories/{$laboratory->id}/modules",
                $modulesStored->all());

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn(AssertableJson $json) => $json->has('0', fn($json) => $json->whereAllType([
            'id' => 'integer',
            'name' => 'string',
            'icon' => 'string',
            'url' => 'string',
            'slug' => 'string',
            '_links' => 'array',
            'active' => 'boolean'
        ]))
        );
    }


}

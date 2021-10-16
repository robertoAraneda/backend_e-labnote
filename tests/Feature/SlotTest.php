<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Slot;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SlotPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SlotTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $role;
    private $user, $model;
    private string $perPage;
    private string $table;

    private string $BASE_URI;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');

        $user = User::factory()->create();


        $this->seed(RoleSeeder::class);
        $this->seed(SlotPermissionsSeeder::class);

        $role = Role::where('name', 'Administrador')->first();

        $user->assignRole($role);

        $modelClass = new Slot();

        $this->user = $user;
        $this->role = $role;
        $this->model = Slot::factory()->create();
        $this->table = $modelClass->getTable();
        $this->base_url = '/api/v1/slots';
        $this->perPage = $modelClass->getPerPage();

    }

    /**
     * @test
     */
    public function se_obtiene_el_valor_por_pagina_por_defecto(): void
    {
        $this->assertEquals(10, $this->perPage);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_del_recurso(): void
    {
        Slot::factory()->count(20)->create();

        $uri = sprintf('%s', $this->base_url);
        $countModels = Slot::count();

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(function (AssertableJson $json) use ($countModels) {
            return $json
                ->has('_links')
                ->has('count')
                ->has('collection', $countModels, function ($json) {
                    $json->whereAllType([
                        'id' => 'integer',
                        'start' => 'string',
                        'end' => 'string',
                        'slot_status_id' => 'integer',
                        'comment' => 'string',
                        'overbooked' => 'boolean',
                        '_links' => 'array'
                    ]);
                });
        });
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_paginada_del_recurso(): void
    {

        $this->withoutExceptionHandling();
        Slot::factory()->count(20)->create();

        $uri = sprintf('%s?page=1', $this->base_url);
        $page = $this->perPage;

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) use ($page) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection', $page, function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'start' => 'string',
                            'end' => 'string',
                            'slot_status_id' => 'integer',
                            'comment' => 'string',
                            'overbooked' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
    }

    /**
     * @test
     */
    public function se_puede_obtener_el_detalle_del_recurso(): void //show
    {
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

        $response = $this->actingAs($this->user, 'api')
            ->getJson($uri);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->where('id', $this->model->id)
            ->where('start', $this->model->start->format('Y-m-d H:i:s'))
            ->where('end', $this->model->end->format('Y-m-d H:i:s'))
            ->etc()
        );
    }

    /**
     * @test
     */
    public function se_puede_crear_un_recurso(): void //store
    {

        $factoryModel = [
            'slot_status_id' => $this->faker->randomNumber(1),
            'start' => $this->faker->dateTime->format('Y-d-mTH:i:s'),
            'end' => $this->faker->dateTime->format('Y-d-mTH:i:s'),
            'overbooked' => false,
            'comment' => $this->faker->text(50),
        ];


        $uri = sprintf("%s", $this->base_url);

        $response = $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('comment', $factoryModel['comment'])
            ->where('overbooked', $factoryModel['overbooked'])
            ->etc()
        );

        $this->assertDatabaseHas($this->table, [
            'comment' => $factoryModel['comment'],
        ]);
    }

    /**
     * @test
     */
    public function se_puede_modificar_un_recurso(): void // update
    {
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

        $this->actingAs($this->user, 'api')
            ->putJson($uri, [
                'comment' => 'comment modificado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('id', $this->model->id)
                ->where('comment', 'comment modificado')
                ->where('overbooked', $this->model->overbooked)
                ->etc()
            );

        $this->assertDatabaseHas($this->table, [
            'comment' => 'comment modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_puede_eliminar_un_recurso(): void //destroy
    {
        $uri = sprintf('%s/%s', $this->base_url, $this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas($this->table, ['id' => $this->model->id]);
        $this->assertSoftDeleted($this->model);
    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_crear_un_recurso_sin_privilegios(): void
    {
        $factoryModel = [
            'slot_status_id' => $this->faker->randomNumber(1),
            'start' => $this->faker->dateTime->format('Y-d-mTH:i:s'),
            'end' => $this->faker->dateTime->format('Y-d-mTH:i:s'),
            'overbooked' => false,
            'comment' => $this->faker->text(50),
        ];

        $this->role->revokePermissionTo('slot.create');

        $uri = sprintf("%s", $this->base_url);

        $this
            ->actingAs($this->user, 'api')
            ->postJson($uri, $factoryModel)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'comment' => $factoryModel['comment'],
        ]);

    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_modificar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('slot.update');

        $uri = sprintf('%s/%s',$this->base_url ,$this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->putJson($uri, [
                'comment' => 'comment modificado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing($this->table, [
            'comment' => 'comment modificado'
        ]);
    }

    /**
     * @test
     */
    public function se_genera_error_http_forbidden_al_eliminar_un_recurso_sin_privilegios(): void
    {
        $this->role->revokePermissionTo('slot.delete');

        $uri = sprintf('%s/%s',$this->base_url ,$this->model->id);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas($this->table, [
            'comment' => $this->model->comment,
        ]);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_mostrar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url , -5);

        $this->actingAs($this->user, 'api')
            ->getJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_editar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $this->actingAs($this->user, 'api')
            ->putJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_found_al_eliminar_si_no_se_encuentra_el_recurso(): void
    {
        $uri = sprintf('%s/%s',$this->base_url ,-5);

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    /**
     * @test
     */
    public function se_obtiene_error_http_not_aceptable_si_parametro_no_es_numerico_al_buscar(): void
    {
        $uri = sprintf('%s/%s',$this->base_url,'string');

        $this->actingAs($this->user, 'api')
            ->deleteJson($uri)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_el_limite_del_paginador(): void
    {
        Slot::factory()->count(20)->create();

        $list = Slot::count();

        $DEFAULT_PAGINATE = 5;

        $mod = $list % $DEFAULT_PAGINATE;

        $pages = intval(ceil($list / $DEFAULT_PAGINATE));

        for ($i = 1; $i <= $pages; $i++) {
            $response = $this->actingAs($this->user, 'api')
                ->getJson(sprintf('%s?page=%s&paginate=%s', $this->base_url, $i, $DEFAULT_PAGINATE))
                ->assertStatus(Response::HTTP_OK);

            if ($i < $pages) {
                $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($DEFAULT_PAGINATE, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }

            }
            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'start' => 'string',
                            'end' => 'string',
                            'slot_status_id' => 'integer',
                            'comment' => 'string',
                            'overbooked' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);

    }

    /**
     * @test
     */
    public function se_puede_obtener_una_lista_cuando_se_modifica_la_pagina(): void
    {
        Slot::factory()->count(20)->create();

        $list = Slot::count();

        $pages = intval(ceil($list / $this->perPage));
        $mod = $list % $this->perPage;

        for ($i = 1; $i <= $pages; $i++) {

            $uri = sprintf('%s?page=%s', $this->base_url, $i);

            $response = $this
                ->actingAs($this->user, 'api')
                ->getJson($uri)
                ->assertStatus(Response::HTTP_OK);

            if ($i < $pages) {
                $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
            } else {
                if ($mod == 0) {
                    $this->assertEquals($this->perPage, collect($response['data']['collection'])->count());
                } else {
                    $this->assertEquals($mod, collect($response['data']['collection'])->count());
                }
            }

            $response->assertJson(function (AssertableJson $json) {
                return $json
                    ->has('links')
                    ->has('meta')
                    ->has('data.collection.0', function ($json) {
                        $json->whereAllType([
                            'id' => 'integer',
                            'start' => 'string',
                            'end' => 'string',
                            'slot_status_id' => 'integer',
                            'comment' => 'string',
                            'overbooked' => 'boolean',
                            '_links' => 'array'
                        ]);
                    });
            });
        }

        $this->assertDatabaseCount($this->table, $list);
    }
}

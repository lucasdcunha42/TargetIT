<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    private function getValidUserData()
    {
        return [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'cpf' => '123.456.789-00',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ];
    }

    /** @test */
    public function cria_um_usuario_via_endpoint_e_retorna_todos_os_dados()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'api');

        $userData = $this->getValidUserData();

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'cpf',
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'cpf' => $userData['cpf'],
        ]);

        $userDetails = $response->json()['user'];

        $this->assertEquals($userData['name'], $userDetails['name']);
        $this->assertEquals($userData['email'], $userDetails['email']);
        $this->assertEquals($userData['phone'], $userDetails['phone']);
        $this->assertEquals($userData['cpf'], $userDetails['cpf']);
        $this->assertArrayNotHasKey('password', $userDetails);
    }

    /** @test */
    public function nao_permite_criar_usuario_com_email_duplicado()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Primeiro cria um usuário
        User::factory()->create(['email' => 'joao@example.com']);

        // Tenta criar outro usuário com o mesmo email
        $userData = $this->getValidUserData();

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function nao_permite_criar_usuario_com_cpf_duplicado()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Primeiro cria um usuário
        User::factory()->create(['cpf' => '123.456.789-00']);

        // Tenta criar outro usuário com o mesmo CPF
        $userData = $this->getValidUserData();

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['cpf']);
    }

    /** @test */
    public function valida_campos_obrigatorios()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function valida_formato_do_email()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $userData = $this->getValidUserData();
        $userData['email'] = 'email-invalido';

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function senha_deve_ter_pelo_menos_6_caracteres()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $userData = $this->getValidUserData();
        $userData['password'] = '123';
        $userData['password_confirmation'] = '123';

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function senha_deve_ser_confirmada()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $userData = $this->getValidUserData();
        $userData['password_confirmation'] = 'senha-diferente';

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }
}

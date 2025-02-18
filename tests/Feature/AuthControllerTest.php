<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $token;

    protected $endPoint = '/api/v1/login';

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'phone' => '(11) 98765-4321',
            'password' => bcrypt('admin_password')
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'email' => 'user@example.com',
            'cpf' => '98765432101',
            'phone' => '(11) 91234-5678',
            'password' => bcrypt('user_password')
        ]);

        $this->token = JWTAuth::fromUser($this->adminUser);
    }

    /** @test */
    public function admin_pode_fazer_login_com_credenciais_validas()
    {
        $response = $this->postJson($this->endPoint, [
            'email' => 'admin@example.com',
            'password' => 'admin_password'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['token']);

        $token = $response->json('token');
        $this->assertNotNull($token);

        $user = JWTAuth::setToken($token)->authenticate();
        $this->assertNotNull($user);
        $this->assertEquals('admin@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function usuario_regular_pode_fazer_login_com_credenciais_validas()
    {
        $response = $this->postJson($this->endPoint, [
            'email' => 'user@example.com',
            'password' => 'user_password'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['token']);

        $token = $response->json('token');
        $this->assertNotNull($token);

        $user = JWTAuth::setToken($token)->authenticate();
        $this->assertNotNull($user);
        $this->assertEquals('user@example.com', $user->email);
        $this->assertEquals('user', $user->role);
    }
    /** @test */
    public function login_falha_com_credenciais_invalidas()
    {
        $response = $this->postJson($this->endPoint, [
            'email' => 'admin@example.com',
            'password' => 'senha_errada'
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Invalid credentials!'
        ]);
    }
    /** @test */
    public function login_requer_email_e_senha()
    {
        $response = $this->postJson($this->endPoint, []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email', 'password']);
    }
    /** @test */
    public function login_requer_email_valido()
    {
        $response = $this->postJson($this->endPoint, [
            'email' => 'email_invalido',
            'password' => 'admin_password'
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }
    /** @test */
    public function usuario_nao_encontrado_retorna_erro()
    {
        $response = $this->postJson($this->endPoint, [
            'email' => 'naoexiste@example.com',
            'password' => 'qualquer_senha'
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Invalid credentials!'
        ]);
    }

}

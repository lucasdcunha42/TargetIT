<?php

namespace Tests\Feature\Api\V1;

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

        // Criando usuário admin
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'phone' => '(11) 98765-4321',
            'password' => bcrypt('admin_password')
        ]);

        // Criando usuário regular
        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'email' => 'user@example.com',
            'cpf' => '98765432101',
            'phone' => '(11) 91234-5678',
            'password' => bcrypt('user_password')
        ]);

        // Gerando token JWT para o admin
        $this->token = JWTAuth::fromUser($this->adminUser);
    }

    /** @test */
    public function admin_pode_fazer_login_com_credenciais_validas()
    {
        // Fazer requisição POST para o endpoint de login
        $response = $this->postJson($this->endPoint, [
            'email' => 'admin@example.com',
            'password' => 'admin_password'
        ]);

        // Verificar se a resposta tem status 200 (OK)
        $response->assertStatus(200);

        // Verificar se a resposta contém o token JWT
        $response->assertJsonStructure(['token']);

        // Verificar se o token é válido
        $token = $response->json('token');
        $this->assertNotNull($token);

        // Verificar se conseguimos autenticar com o token recebido
        $user = JWTAuth::setToken($token)->authenticate();
        $this->assertNotNull($user);
        $this->assertEquals('admin@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
    }


    public function usuario_regular_pode_fazer_login_com_credenciais_validas()
    {
        // Fazer requisição POST para o endpoint de login
        $response = $this->postJson($this->endPoint, [
            'email' => 'user@example.com',
            'password' => 'user_password'
        ]);

        // Verificar se a resposta tem status 200 (OK)
        $response->assertStatus(200);

        // Verificar se a resposta contém o token JWT
        $response->assertJsonStructure(['token']);

        // Verificar se o token é válido
        $token = $response->json('token');
        $this->assertNotNull($token);

        // Verificar se conseguimos autenticar com o token recebido
        $user = JWTAuth::setToken($token)->authenticate();
        $this->assertNotNull($user);
        $this->assertEquals('user@example.com', $user->email);
        $this->assertEquals('user', $user->role);
    }

    public function login_falha_com_credenciais_invalidas()
    {
        // Tentar login com senha errada
        $response = $this->postJson($this->endPoint, [
            'email' => 'admin@example.com',
            'password' => 'senha_errada'
        ]);

        // Verificar se a resposta tem status 401 (Unauthorized)
        $response->assertStatus(401);

        // Verificar a mensagem de erro
        $response->assertJson([
            'message' => 'Credenciais inválidas!'
        ]);
    }

    public function login_requer_email_e_senha()
    {
        // Tentar login sem informar email e senha
        $response = $this->postJson($this->endPoint, []);

        // Verificar se a resposta tem status 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Verificar se contém erros de validação para email e senha
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function login_requer_email_valido()
    {
        // Tentar login com email em formato inválido
        $response = $this->postJson($this->endPoint, [
            'email' => 'email_invalido',
            'password' => 'admin_password'
        ]);

        // Verificar se a resposta tem status 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Verificar se contém erro de validação para email
        $response->assertJsonValidationErrors(['email']);
    }

    public function usuario_nao_encontrado_retorna_erro()
    {
        // Tentar login com email que não existe no sistema
        $response = $this->postJson($this->endPoint, [
            'email' => 'naoexiste@example.com',
            'password' => 'qualquer_senha'
        ]);

        // Verificar se a resposta tem status 401 (Unauthorized)
        $response->assertStatus(401);

        // Verificar a mensagem de erro
        $response->assertJson([
            'message' => 'Credenciais inválidas!'
        ]);
    }

}

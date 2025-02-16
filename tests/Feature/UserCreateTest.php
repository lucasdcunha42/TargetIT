<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_created_correctly(): void
    {
        // Dados do usuário a serem enviados na requisição
        $userData = [
            'name' => 'Lucas Cunha',
            'email' => 'lucas@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Envia uma requisição POST para registrar o usuário
        $response = $this->postJson('/api/v1/register', $userData);

        // Verifica se o status da resposta é 201 (Created)
        $response->assertStatus(201);

        // Verifica se o usuário foi salvo no banco de dados
        $this->assertDatabaseHas('users', [
            'email' => 'lucas@example.com',
        ]);
    }
}

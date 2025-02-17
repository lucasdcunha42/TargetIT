<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'phone' => '(11) 98765-4321'
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'email' => 'user@example.com',
            'cpf' => '98765432101',
            'phone' => '(11) 91234-5678'
        ]);

        $this->token = JWTAuth::fromUser($this->adminUser);
    }

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '(11) 92345-6789',
            'cpf' => '11122233344',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('message', 'User Created Successfully!')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'user' => ['id', 'name', 'email', 'phone', 'cpf', 'created_at', 'updated_at'],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'cpf' => '11122233344',
            'phone' => '(11) 92345-6789',
            'role' => 'user'
        ]);
    }

    public function test_cannot_create_user_without_authentication()
    {
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '(11) 92345-6789',
            'cpf' => '11122233355',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(401);
    }

    public function test_cannot_create_user_with_duplicate_email()
    {
        $userData = [
            'name' => 'Duplicate Email',
            'email' => $this->regularUser->email, // Using existing email
            'phone' => '(11) 92345-6789',
            'cpf' => '44455566677',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_user_with_duplicate_cpf()
    {
        $userData = [
            'name' => 'Duplicate CPF',
            'email' => 'new@example.com',
            'phone' => '(11) 92345-6789',
            'cpf' => $this->regularUser->cpf,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cpf']);
    }

    public function test_can_show_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/users/' . $this->regularUser->id);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('message', 'Profile Data')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'phone',
                         'cpf',
                         'role',
                         'created_at',
                         'updated_at'
                     ],
                 ]);
    }

    public function test_cannot_show_user_without_authentication()
    {
        $response = $this->getJson('/api/v1/users/' . $this->regularUser->id);

        $response->assertStatus(401);
    }

    public function test_can_update_user()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'phone' => '(11) 99999-8888',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/v1/users/' . $this->regularUser->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('message', 'User updated successfully!')
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'phone',
                         'cpf',
                         'role',
                         'created_at',
                         'updated_at',
                         'address'
                     ],
                     'message',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'name' => 'Updated Name',
            'phone' => '(11) 99999-8888',
        ]);
    }

    public function test_cannot_update_user_without_authentication()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'phone' => '(11) 99999-8888',
        ];

        $response = $this->putJson('/api/v1/users/' . $this->regularUser->id, $updatedData);

        $response->assertStatus(401);
    }

    public function test_cannot_update_email_to_existing_one()
    {
        $updatedData = [
            'email' => $this->adminUser->email, // Trying to use an existing email
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/v1/users/' . $this->regularUser->id, $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_update_cpf_to_existing_one()
    {
        $updatedData = [
            'cpf' => $this->adminUser->cpf, // Trying to use an existing CPF
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/v1/users/' . $this->regularUser->id, $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cpf']);
    }

    public function test_can_delete_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/v1/users/' . $this->regularUser->id);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('message', 'User deleted successfully!');

        // Verify soft delete
        $this->assertSoftDeleted('users', [
            'id' => $this->regularUser->id,
        ]);
    }

    public function test_cannot_delete_user_without_authentication()
    {
        $response = $this->deleteJson('/api/v1/users/' . $this->regularUser->id);

        $response->assertStatus(401);
    }
}

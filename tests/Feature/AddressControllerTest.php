<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Auth\Access\AuthorizationException;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that an unauthorized user cannot store an address for another user.
     */
    public function test_unauthorized_user_cannot_store_address()
    {
        // Create a target user and another (unauthorized) user.
        $targetUser = User::factory()->create();
        $unauthorizedUser = User::factory()->create();

        $data = [
            'street' => $this->faker->streetName,
            'number' => '100',
            'district' => $this->faker->cityPrefix,
            'complement' => 'Apto 101',
            'zip_code' => '12345678',
        ];

        // Acting as a user that is not allowed to update the target user's address.
        $response = $this->actingAs($unauthorizedUser, 'api')
                         ->postJson("/api/v1/users/{$targetUser->id}/addresses", $data);

        $response->assertStatus(403);
    }

    /**
     * Test that a new address is created when the user has no existing address.
     */
    public function test_creates_address_when_user_has_no_existing_address()
    {
        // Create a user without an address.
        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'phone' => '(11) 98765-4321'
        ]);

        $data = [
            'street' => 'Rua Exemplo',
            'number' => '100',
            'district' => 'Bairro Exemplo',
            'complement' => 'Apto 101',
            'zip_code' => '12345678',
        ];

        $response = $this->actingAs($user, 'api')
                         ->postJson("/api/v1/users/{$user->id}/addresses", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Endereço criado com sucesso',
                     'address' => [
                         'street' => $data['street'],
                         'number' => $data['number'],
                         'district' => $data['district'],
                         'complement' => $data['complement'],
                         'zip_code' => $data['zip_code'],
                     ],
                 ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => $data['street'],
            'number' => $data['number'],
            'district' => $data['district'],
            'complement' => $data['complement'],
            'zip_code' => $data['zip_code'],
        ]);
    }

    /**
     * Test that an existing address is updated when the user already has one.
     */
    public function test_updates_address_when_user_already_has_existing_address()
    {
        
        // Create a user and an existing address for that user.
        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'phone' => '(11) 98765-4321'
        ]);

        $existingAddress = Address::factory()->create([
            'user_id' => $user->id,
            'street' => 'Rua Antiga',
            'number' => '50',
            'district' => 'Bairro Antigo',
            'complement' => 'Casa',
            'zip_code' => '87654321',
        ]);

        $data = [
            'street' => 'Rua Nova',
            'number' => '150',
            'district' => 'Bairro Novo',
            'complement' => 'Apto 202',
            'zip_code' => '12345678',
        ];

        $response = $this->actingAs($user, 'api')
                         ->postJson("/api/v1/users/{$user->id}/addresses", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Endereço atualizado com sucesso',
                     'address' => [
                         'street' => $data['street'],
                         'number' => $data['number'],
                         'district' => $data['district'],
                         'complement' => $data['complement'],
                         'zip_code' => $data['zip_code'],
                     ],
                 ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $existingAddress->id,
            'user_id' => $user->id,
            'street' => $data['street'],
            'number' => $data['number'],
            'district' => $data['district'],
            'complement' => $data['complement'],
            'zip_code' => $data['zip_code'],
        ]);
    }
}
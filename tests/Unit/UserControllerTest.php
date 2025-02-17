<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Carbon;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /**
     * Fechamento dos mocks após cada teste.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Testa o método show() para verificar se os dados do usuário são retornados corretamente.
     */
    public function testShowReturnsUserData()
    {
        // Cria um objeto usuário com dados fictícios.
        $user = new User([
            'id'         => 1,
            'name'       => 'John Doe',
            'email'      => 'john@example.com',
            'phone'      => '11999999999',
            'cpf'        => '12345678900',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $controller = new UserController();

        $response = $controller->show($user);

        $this->assertEquals(200, $response->getStatusCode());

        // Decodifica a resposta JSON.
        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Profile Data', $data['message']);
        $this->assertEquals($user->toArray(), $data['user']);
    }

    /**
     * Testa o método store() para criação de um usuário com sucesso.
     */
    public function testStoreCreatesUserSuccessfully()
    {
        // Dados validados simulados para criação
        $validatedData = [
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => 'password123',
            'phone'    => '11988887777',
            'cpf'      => '09876543210',
        ];

        // Usuário autenticado (dummy)
        $authenticatedUser = User::factory()->create([
            'name' => 'Auth User',
            'email' => 'auth@example.com',
            'password' => bcrypt('password'),
            'phone' => '11999999999',
            'cpf' => '12345678901'
        ]);

        // Mock do request
        $request = Mockery::mock(CreateUserRequest::class);
        $request->shouldReceive('validated')
                ->once()
                ->andReturn($validatedData);

        // Mock parcial do controller
        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('authorize')
                   ->once()
                   ->with('store', \Mockery::type(User::class));

        // Executa o método
        $response = $controller->store($request, $authenticatedUser);

        // Assertions
        $this->assertEquals(201, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('User Created Successfully!', $data['message']);

        // Verifica se os dados do usuário criado estão corretos
        $this->assertEquals($validatedData['name'], $data['user']['name']);
        $this->assertEquals($validatedData['email'], $data['user']['email']);
        $this->assertEquals($validatedData['phone'], $data['user']['phone']);
        $this->assertEquals($validatedData['cpf'], $data['user']['cpf']);

        // Verifica se o usuário foi realmente criado no banco
        $this->assertDatabaseHas('users', [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'cpf' => $validatedData['cpf']
        ]);
    }

    /**
     * Testa o método update() para verificar se os dados do usuário são atualizados corretamente.
     */
    public function testUpdateUserSuccessfully()
    {
        // Dados validados simulados para atualização.
        $validatedData = [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '11977776666',
            'cpf'   => '11223344556',
        ];

        // Cria um mock parcial do usuário para simular os métodos update() e load().
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('update')
             ->once()
             ->with($validatedData)
             ->andReturn(true);
        $user->shouldReceive('load')
             ->once()
             ->with('address')
             ->andReturnSelf();

        // Define alguns atributos fictícios.
        $user->id    = 1;
        $user->name  = 'Old Name';
        $user->email = 'old@example.com';

        // Cria um partial mock do controller para simular a autorização.
        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('authorize')
                   ->once()
                   ->with('update', $user);

        // Cria um stub para o UpdateUserRequest.
        $request = Mockery::mock(UpdateUserRequest::class);
        $request->shouldReceive('validated')
                ->once()
                ->andReturn($validatedData);

        $response = $controller->update($request, $user);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('User updated successfully!', $data['message']);
        $this->assertEquals($user->toArray(), $data['data']);
    }

    /**
     * Testa o método destroy() para verificar se o usuário é excluído (soft delete) corretamente.
     */
    public function testDestroyUserSuccessfully()
    {
        // Cria um mock parcial do usuário, simulando o método delete().
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('delete')
             ->once()
             ->andReturn(true);
        $user->id = 1;

        // Cria um partial mock do controller para simular a autorização.
        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('authorize')
                   ->once()
                   ->with('delete', $user);

        $response = $controller->destroy($user);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('User deleted successfully!', $data['message']);
    }
}

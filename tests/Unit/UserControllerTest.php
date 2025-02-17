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
     * Close mocks after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Tests the show() method to verify if user data is returned correctly.
     */
    public function testShowReturnsUserData()
    {
        // Creates a user object with fictitious data.
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

        // Decodes the JSON response.
        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Profile Data', $data['message']);
        $this->assertEquals($user->toArray(), $data['user']);
    }

    /**
     * Tests the store() method for successful user creation.
     */
    public function testStoreCreatesUserSuccessfully()
    {
        // Simulated validated data for creation
        $validatedData = [
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => 'password123',
            'phone'    => '11988887777',
            'cpf'      => '09876543210',
        ];

        // Authenticated user (dummy)
        $authenticatedUser = User::factory()->create([
            'name' => 'Auth User',
            'email' => 'auth@example.com',
            'password' => bcrypt('password'),
            'phone' => '11999999999',
            'cpf' => '12345678901'
        ]);

        // Mock request
        $request = Mockery::mock(CreateUserRequest::class);
        $request->shouldReceive('validated')
                ->once()
                ->andReturn($validatedData);

        // Partial mock of the controller
        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('authorize')
                   ->once()
                   ->with('store', \Mockery::type(User::class));

        // Executes the method
        $response = $controller->store($request, $authenticatedUser);

        // Assertions
        $this->assertEquals(201, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('User Created Successfully!', $data['message']);

        // Verifies if the created user data is correct
        $this->assertEquals($validatedData['name'], $data['user']['name']);
        $this->assertEquals($validatedData['email'], $data['user']['email']);
        $this->assertEquals($validatedData['phone'], $data['user']['phone']);
        $this->assertEquals($validatedData['cpf'], $data['user']['cpf']);

        // Verifies if the user was actually created in the database
        $this->assertDatabaseHas('users', [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'cpf' => $validatedData['cpf']
        ]);
    }

    /**
     * Tests the update() method to verify if user data is updated correctly.
     */
    public function testUpdateUserSuccessfully()
    {
        // Simulated validated data for update.
        $validatedData = [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '11977776666',
            'cpf'   => '11223344556',
        ];

        // Creates a partial mock of the user to simulate the update() and load() methods.
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('update')
             ->once()
             ->with($validatedData)
             ->andReturn(true);
        $user->shouldReceive('load')
             ->once()
             ->with('address')
             ->andReturnSelf();

        // Defines some fictitious attributes.
        $user->id    = 1;
        $user->name  = 'Old Name';
        $user->email = 'old@example.com';

        // Creates a partial mock of the controller to simulate authorization.
        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('authorize')
                   ->once()
                   ->with('update', $user);

        // Creates a stub for the UpdateUserRequest.
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
     * Tests the destroy() method to verify if the user is deleted (soft delete) correctly.
     */
    public function testDestroyUserSuccessfully()
    {
        // Creates a partial mock of the user, simulating the delete() method.
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('delete')
             ->once()
             ->andReturn(true);
        $user->id = 1;

        // Creates a partial mock of the controller to simulate authorization.
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

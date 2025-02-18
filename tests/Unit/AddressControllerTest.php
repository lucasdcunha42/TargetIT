<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /**
     * Close Mockery expectations.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that an existing address is updated when the user already has one.
     */
    public function test_store_updates_existing_address()
    {
        $data = [
            'street'       => 'New Street',
            'number'       => '150',
            'complement'   => 'New Complement',
            'neighborhood' => 'New Neighborhood',
            'city'         => 'New City',
            'state'        => 'NS',
            'zip_code'     => '11111-111',
        ];

        $request = Mockery::mock(AddressRequest::class);
        $request->shouldReceive('validated')
                ->once()
                ->andReturn($data);

        $user = new User();
        $user->id = 1;

        $address = Mockery::mock(Address::class)->makePartial();
        $address->id = 2;

        $address->shouldReceive('update')
                ->once()
                ->with($data)
                ->andReturnTrue();

        $user->address = $address;

        $controller = new class extends AddressController {
            public function authorize($ability, $arguments = [])
            {
            }
        };

        $response = $controller->store($request, $user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);

        $this->assertEquals('Endereço atualizado com sucesso', $responseData['message']);
        $this->assertEquals($address->toArray(), (array) $responseData['address']);
    }
}

<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;
use Mockery;

class AuthControllerTest extends TestCase
{
    /**
     * Clear mocks after each test.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Tests if login returns the token when credentials are valid.
     *
     * @return void
     */
    public function test_login_returns_token_when_credentials_are_valid()
    {
        $credentials = [
            'email'    => 'user@example.com',
            'password' => 'password123'
        ];

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.token';

        // Simulates the behavior of JWTAuth for valid credentials
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn($token);

        // Creates a mock for LoginRequest
        $loginRequest = Mockery::mock(LoginRequest::class);
        $loginRequest->shouldReceive('validated')
            ->once()
            ->andReturn($credentials);

        $controller = new AuthController();
        $response = $controller->login($loginRequest);

        // Checks if the HTTP status is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Converts the response content to an array and checks if the token is present and correct
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertEquals($token, $responseData['token']);
    }

    /**
     * Tests if login returns an error message when credentials are invalid.
     *
     * @return void
     */
    public function test_login_returns_error_when_credentials_are_invalid()
    {
        $credentials = [
            'email'    => 'user@example.com',
            'password' => 'password123'
        ];

        // Simulates the behavior of JWTAuth for invalid credentials (returning false)
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn(false);

        // Creates a mock for LoginRequest
        $loginRequest = Mockery::mock(LoginRequest::class);
        $loginRequest->shouldReceive('validated')
            ->once()
            ->andReturn($credentials);

        $controller = new AuthController();
        $response = $controller->login($loginRequest);

        // Checks if the HTTP status is 401
        $this->assertEquals(401, $response->getStatusCode());

        // Converts the response content to an array and checks if the error message is correct
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Invalid credentials!', $responseData['message']);
    }
}

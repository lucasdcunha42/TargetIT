<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;


class CreateUserRequestTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Instance of CreateUserRequest for use in tests.
     *
     * @var \App\Http\Requests\CreateUserRequest
     */
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new CreateUserRequest();
    }

    /**
     * Tests if the authorize method returns true.
     */
    public function testAuthorizeReturnsTrue()
    {
        $this->assertTrue($this->request->authorize());
    }

    /**
     * Checks if the validation rules are defined correctly.
     */
    public function testRules()
    {
        $rules = $this->request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertArrayHasKey('phone', $rules);
        $this->assertArrayHasKey('cpf', $rules);

        $this->assertEquals('required|string|max:255', $rules['name']);
        $this->assertEquals('required|email|unique:users', $rules['email']);
        $this->assertEquals('required|min:6|confirmed', $rules['password']);
        $this->assertEquals('sometimes|max:20', $rules['phone']);
        $this->assertEquals('sometimes|unique:users|max:14', $rules['cpf']);
    }

    /**
     * Checks if the error messages are defined as expected.
     */
    public function testMessages()
    {
        $messages = $this->request->messages();

        $this->assertEquals('The name field is required.', $messages['name.required']);
        $this->assertEquals('The name must be a valid string.', $messages['name.string']);
        $this->assertEquals('The name may not be greater than 255 characters.', $messages['name.max']);

        $this->assertEquals('The email field is required.', $messages['email.required']);
        $this->assertEquals('The email must be a valid email address.', $messages['email.email']);
        $this->assertEquals('The email has already been taken.', $messages['email.unique']);

        $this->assertEquals('The password field is required.', $messages['password.required']);
        $this->assertEquals('The password must be at least 6 characters.', $messages['password.min']);

        $this->assertEquals('The phone must be a valid string.', $messages['phone.string']);
        $this->assertEquals('The phone may not be greater than 20 characters.', $messages['phone.max']);

        $this->assertEquals('The CPF must be a valid string.', $messages['cpf.string']);
        $this->assertEquals('The CPF may not be greater than 14 characters.', $messages['cpf.max']);
    }

    /**
     * Tests if valid data passes validation.
     */
    public function testValidDataPassesValidation()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'phone'                 => '1234567890',
            'cpf'                   => '123.456.789-10',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertTrue($validator->passes());
    }

    /**
     * Tests if validation fails when the "name" field is missing.
     */
    public function testValidationFailsWhenNameIsMissing()
    {
        $data = [
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertEquals('The name field is required.', $validator->errors()->first('name'));
    }

    /**
     * Tests if validation fails for an invalid email.
     */
    public function testValidationFailsWhenEmailIsInvalid()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'invalid-email',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertEquals('The email must be a valid email address.', $validator->errors()->first('email'));
    }

    /**
     * Tests if validation fails when the password is too short.
     */
    public function testValidationFailsWhenPasswordIsShort()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertEquals('The password must be at least 6 characters.', $validator->errors()->first('password'));
    }

    /**
     * Tests if validation fails when the password confirmation does not match.
     */
    public function testValidationFailsWhenPasswordConfirmationDoesNotMatch()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'different',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        // The default message for "confirmed" may vary, so we use assertStringContainsString.
        $this->assertStringContainsString('confirmation', $validator->errors()->first('password'));
    }

    /**
     * Tests if validation fails when the "name" field exceeds the maximum length.
     */
    public function testValidationFailsWhenNameExceedsMaxLength()
    {
        $data = [
            'name'                  => str_repeat('a', 256),
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertEquals('The name may not be greater than 255 characters.', $validator->errors()->first('name'));
    }

    /**
     * Tests if validation fails when the "phone" field exceeds the maximum length.
     */
    public function testValidationFailsWhenPhoneExceedsMaxLength()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'phone'                 => str_repeat('1', 21),
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('phone', $validator->errors()->toArray());
        $this->assertEquals('The phone may not be greater than 20 characters.', $validator->errors()->first('phone'));
    }

    /**
     * Tests if validation fails when the "cpf" field exceeds the maximum length.
     */
    public function testValidationFailsWhenCpfExceedsMaxLength()
    {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'cpf'                   => str_repeat('1', 15),
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('cpf', $validator->errors()->toArray());
        $this->assertEquals('The CPF may not be greater than 14 characters.', $validator->errors()->first('cpf'));
    }
}

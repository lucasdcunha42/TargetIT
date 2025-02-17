<?php

namespace Tests\Unit;

use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AddressRequestTest extends TestCase
{
    /**
     * Tests if the authorize method returns true as defined.
     */
    public function testAuthorizeMethodReturnsTrue()
    {
        $request = new AddressRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Tests if the rules method returns the expected rules.
     */
    public function testRulesMethodReturnsExpectedRules()
    {
        $request = new AddressRequest();
        $rules = $request->rules();

        $expectedRules = [
            'street'    => 'required|string|max:255',
            'number'    => 'required|string|max:10',
            'district'  => 'required|string|max:255',
            'complement'=> 'nullable|string|max:255',
            'zip_code'  => 'required|string|max:10',
        ];

        $this->assertEquals($expectedRules, $rules);
    }

    /**
     * Tests if the messages method returns the expected error messages.
     */
    public function testMessagesMethodReturnsExpectedMessages()
    {
        $request = new AddressRequest();
        $messages = $request->messages();

        $expectedMessages = [
            'street.required'   => 'The street field is required.',
            'street.string'     => 'The street must be a text.',
            'street.max'        => 'The street may not be greater than 255 characters.',

            'number.required'   => 'The number field is required.',
            'number.string'     => 'The number must be a text.',
            'number.max'        => 'The number may not be greater than 10 characters.',

            'district.required' => 'The district field is required.',
            'district.string'   => 'The district must be a text.',
            'district.max'      => 'The district may not be greater than 255 characters.',

            'complement.string' => 'The complement must be a text.',
            'complement.max'    => 'The complement may not be greater than 255 characters.',

            'zip_code.required' => 'The zip code field is required.',
            'zip_code.string'   => 'The zip code must be a text.',
            'zip_code.max'      => 'The zip code may not be greater than 10 characters.',
        ];

        $this->assertEquals($expectedMessages, $messages);
    }

    /**
     * Tests validation with valid data.
     */
    public function testValidationPassesWithValidData()
    {
        $data = [
            'street'    => 'ABC Street',
            'number'    => '123',
            'district'  => 'Downtown',
            'complement'=> 'Apt 45',
            'zip_code'  => '1234567890',
        ];

        // Correcting the mock creation
        $request = $this->createPartialMock(AddressRequest::class, ['authorize']);
        $request->expects($this->any())
                ->method('authorize')
                ->willReturn(true);

        $validator = Validator::make($data, $request->rules(), $request->messages());
        $this->assertFalse($validator->fails());
    }

    /**
     * Tests validation fails when required fields are missing.
     */
    public function testValidationFailsWithMissingRequiredFields()
    {
        $data = [
            'complement' => 'Apt 45',
        ];

        // Correcting the mock creation
        $request = $this->createPartialMock(AddressRequest::class, ['authorize']);
        $request->expects($this->any())
                ->method('authorize')
                ->willReturn(true);

        $validator = Validator::make($data, $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());

        foreach (['street', 'number', 'district', 'zip_code'] as $field) {
            $this->assertTrue($validator->errors()->has($field));
        }
    }
}

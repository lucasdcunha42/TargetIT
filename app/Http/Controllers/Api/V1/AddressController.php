<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(AddressRequest $request, User $user)
    {
        $this->authorize('store', $user);


        $data = $request->validated();

        // Verifica se o usuário já possui um endereço
        $address = $user->address;

        if ($address) {
            $address->update($data);
            return response()->json([
                'message' => 'Endereço atualizado com sucesso',
                'address' => $address
            ],200);
        }

        $data['user_id'] = $user->id;
        $address = Address::create($data);
        return response()->json([
            'message' => 'Endereço criado com sucesso',
            'address' => $address
        ],200);
    }
}

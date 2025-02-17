<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;

/**
 * @group Endereços
 *
 * APIs para gerenciamento de endereços dos usuários
 * @authenticated
 */

class AddressController extends Controller
{

    /**
     * Criar endereço
     * 
     * Adiciona um novo endereço para um usuário.
     * Usuários podem adicionar endereços a suas próprias contas.
     * Administradores podem adicionar endereços a qualquer usuário.
     *
     * @authenticated
     * 
     * @urlParam user integer required ID do usuário. Example: 1
     * @bodyParam street string required Nome da rua. Example: Rua das Flores
     * @bodyParam number string required Número. Example: 123
     * @bodyParam complement string Complemento. Example: Apto 45
     * @bodyParam neighborhood string required Bairro. Example: Centro
     * @bodyParam city string required Cidade. Example: São Paulo
     * @bodyParam state string required Estado. Example: SP
     * @bodyParam zip_code string required CEP. Example: 01234-567
     * 
     * @response 201 {
     *   "message": "Endereço criado com sucesso",
     *   "data": {
     *     "id": 1,
     *     "street": "Rua das Flores",
     *     "number": "123",
     *     "complement": "Apto 45",
     *     "neighborhood": "Centro",
     *     "city": "São Paulo",
     *     "state": "SP",
     *     "zip_code": "01234-567"
     *   }
     * }
     * 
     * @response 403 {
     *   "message": "Não autorizado. Você só pode adicionar endereços à sua própria conta."
     * }
     */
    
    public function store(AddressRequest $request, User $user)
    {
        $this->authorize('store', $user);


        $data = $request->validated();

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

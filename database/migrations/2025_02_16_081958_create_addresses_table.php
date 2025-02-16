<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('addresses', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');  // Relacionamento com o usuário
        $table->string('logradouro');
        $table->string('numero');
        $table->string('bairro');
        $table->string('complemento')->nullable();
        $table->string('cep');
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');  // Garantir que o endereço seja excluído se o usuário for excluído
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

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
        $table->string('street');                   // Logradouro
        $table->string('number');                   // Número
        $table->string('district');                 // Bairro
        $table->string('complement')->nullable();   // Complemento
        $table->string('zip_code');                 // CEP
        $table->timestamps();

        $table->foreignId('user_id')->constrained();
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

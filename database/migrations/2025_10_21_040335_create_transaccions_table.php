<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaccion', function (Blueprint $table) {
        $table->id('idtransaccion');
        $table->decimal('monto', 10, 2);
        $table->date('fecha');
        $table->string('descripcion', 255)->nullable();
        $table->unsignedBigInteger('idforma_pago');
        $table->unsignedBigInteger('idUsuario');
        $table->string('alias_destinatario', 45)->nullable();
        $table->string('nombre_destinatario', 45)->nullable();
        $table->unsignedBigInteger('idcategoria');

        $table->foreign('idUsuario')->references('idUsuario')->on('usuario');
        $table->foreign('idcategoria')->references('idcategoria')->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaccions');
    }
};

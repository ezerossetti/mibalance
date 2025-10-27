<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('alertas', function (Blueprint $table) {
        $table->id(); // ID de la alerta (BIGINT UNSIGNED por defecto, está bien)

        // --- CORRECCIÓN AQUÍ ---
        // Creamos las columnas con el tipo INT UNSIGNED (compatible con INT(11) de tus tablas)
        $table->unsignedInteger('idUsuario');
        $table->unsignedInteger('idcategoria');
        // --- FIN CORRECCIÓN ---

        $table->string('tipo')->default('gasto_mayor_a');
        $table->decimal('limite', 10, 2);
        $table->boolean('activa')->default(true);
        $table->timestamps();

        // --- Definimos las claves foráneas por separado ---
        $table->foreign('idUsuario')
              ->references('idUsuario')->on('usuario') // Referencia a la tabla 'usuario' columna 'idUsuario'
              ->cascadeOnDelete(); // Si se borra el usuario, se borra la alerta

        $table->foreign('idcategoria')
              ->references('idcategoria')->on('categoria') // Referencia a la tabla 'categoria' columna 'idcategoria'
              ->cascadeOnDelete(); // Si se borra la categoría, se borra la alerta
    });
}

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};

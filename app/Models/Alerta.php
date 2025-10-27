<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas'; // Nombre de la tabla

    // Columnas que se pueden llenar masivamente (si usamos create())
    protected $fillable = [
        'idUsuario',
        'idcategoria',
        'tipo',
        'limite',
        'activa',
    ];

    // Relación: Una alerta pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    // Relación: Una alerta pertenece a una Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategoria', 'idcategoria');
    }
}
